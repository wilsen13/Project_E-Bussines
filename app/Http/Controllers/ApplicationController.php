<?php
namespace App\Http\Controllers;
use App\Models\Application;
use App\Models\Job;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function store(Request $request, $jobId) {
        $user = Auth::user();
        if ($user->role !== 'JOB_SEEKER') {
            return back()->with('error', 'Hanya pencari kerja yang bisa melamar.');
        }

        $job = Job::findOrFail($jobId);

        $existing = Application::where('jobID', $jobId)->where('jobSeekerID', $user->userID)->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah melamar pekerjaan ini.');
        }

        Application::create([
            'applicationID' => Str::uuid(),
            'jobID' => $jobId,
            'jobSeekerID' => $user->userID,
            'letter' => $request->letter,
            'status' => 'APPLIED',
        ]);
        
        Notification::create([
            'notificationID' => Str::uuid(),
            'userID' => $user->userID,
            'title' => 'Lamaran Terkirim',
            'message' => 'Anda telah berhasil melamar pekerjaan: ' . $job->title,
        ]);

        Notification::create([
            'notificationID' => Str::uuid(),
            'userID' => $job->employerID,
            'title' => 'Lamaran Masuk Baru',
            'message' => 'Ada pelamar baru untuk pekerjaan Anda: ' . $job->title . '. Silakan periksa tab Lamaran Masuk.',
        ]);

        return redirect()->route('notifications.index')->with('success', 'Lamaran berhasil dikirim!');
    }

    public function notifications() {
        $user = Auth::user();
        $notifications = Notification::where('userID', $user->userID)->latest('sentAt')->get();
        $applications = [];
        $contracts = [];
        
        if ($user->role === 'JOB_SEEKER') {
            $applications = Application::with('job.employer')->where('jobSeekerID', $user->userID)->latest('createdAt')->get();
            $contracts = \App\Models\Contract::with(['job.employer', 'payment'])->where('jobSeekerID', $user->userID)->latest('startAt')->get();
        } else if ($user->role === 'EMPLOYER') {
            $applications = Application::with(['job', 'jobSeeker.user'])->whereHas('job', function($q) use ($user) {
                $q->where('employerID', $user->userID);
            })->where('status', 'APPLIED')->latest('createdAt')->get();
            $contracts = \App\Models\Contract::with(['job', 'jobSeeker.user', 'payment'])->where('employerID', $user->userID)->latest('startAt')->get();
        }

        $reviewedContracts = \App\Models\Review::where('reviewerUserID', $user->userID)
            ->pluck('contractID')
            ->toArray();

        return view('notifications.index', compact('notifications', 'applications', 'contracts', 'reviewedContracts'));
    }

    public function process(Request $request, $applicationId) {
        $user = Auth::user();
        if ($user->role !== 'EMPLOYER') return back();

        $application = Application::with('job')->where('applicationID', $applicationId)->firstOrFail();
        if ($application->job->employerID !== $user->userID) return back();

        $action = $request->input('action');

        if ($action === 'APPROVE') {
            $application->status = 'HIRED';
            $application->save();

            // Create Contract
            $contractId = Str::uuid();
            \App\Models\Contract::create([
                'contractID' => $contractId,
                'jobID' => $application->jobID,
                'employerID' => $user->userID,
                'jobSeekerID' => $application->jobSeekerID,
                'status' => 'ACTIVE'
            ]);

            // Create Escrow Payment
            // We need the seeker's wallet
            $seekerWallet = \App\Models\Wallet::where('userID', $application->jobSeekerID)->first();
            if ($seekerWallet) {
                \App\Models\Payment::create([
                    'paymentID' => Str::uuid(),
                    'walletID' => $seekerWallet->walletID,
                    'contractID' => $contractId,
                    'amount' => $application->job->payAmount,
                    'status' => 'HELD'
                ]);
            }

            // Close the Job
            $application->job->jobStatus = 'CLOSED';
            $application->job->save();

            // Reject all other applications for this job
            Application::where('jobID', $application->jobID)
                ->where('applicationID', '!=', $applicationId)
                ->update(['status' => 'REJECTED']);

            Notification::create([
                'notificationID' => Str::uuid(),
                'userID' => $application->jobSeekerID,
                'title' => 'Lamaran Diterima!',
                'message' => 'Selamat! Lamaran Anda untuk pekerjaan "' . $application->job->title . '" telah diterima. Pekerjaan sekarang aktif.',
            ]);

            return back()->with('success', 'Pelamar berhasil diterima. Kontrak pekerjaan telah dibuat.');
        } elseif ($action === 'REJECT') {
            $application->status = 'REJECTED';
            $application->save();

            Notification::create([
                'notificationID' => Str::uuid(),
                'userID' => $application->jobSeekerID,
                'title' => 'Lamaran Ditolak',
                'message' => 'Maaf, lamaran Anda untuk pekerjaan "' . $application->job->title . '" telah ditolak.',
            ]);

            return back()->with('success', 'Pelamar telah ditolak.');
        }

        return back();
    }

    public function markComplete(Request $request, $contractId) {
        $user = Auth::user();
        if ($user->role !== 'JOB_SEEKER') return back();

        $request->validate([
            'proof_of_work' => 'required|string|max:2000',
        ], [
            'proof_of_work.required' => 'Bukti pekerjaan wajib diisi sebelum mengirim.',
        ]);

        $contract = \App\Models\Contract::with('job')->where('contractID', $contractId)->where('jobSeekerID', $user->userID)->firstOrFail();
        
        if ($contract->status === 'ACTIVE') {
            $contract->status = 'WAITING_REVIEW';
            $contract->proof_of_work = $request->input('proof_of_work');
            $contract->revision_notes = null; // Clear previous revision notes
            $contract->save();

            Notification::create([
                'notificationID' => Str::uuid(),
                'userID' => $contract->employerID,
                'title' => 'Bukti Pekerjaan Dikirim',
                'message' => 'Freelancer telah mengirimkan bukti pekerjaan untuk "' . $contract->job->title . '". Silakan periksa dan setujui atau minta revisi.',
            ]);
        }

        return back()->with('success', 'Bukti pekerjaan berhasil dikirim! Menunggu persetujuan Employer.');
    }

    public function confirmComplete(Request $request, $contractId) {
        $user = Auth::user();
        if ($user->role !== 'EMPLOYER') return back();

        $contract = \App\Models\Contract::with(['payment', 'job'])->where('contractID', $contractId)->where('employerID', $user->userID)->firstOrFail();
        
        $action = $request->input('action');

        if ($contract->status === 'WAITING_REVIEW' && $action === 'APPROVE') {
            $contract->status = 'COMPLETED';
            $contract->endAt = now();
            $contract->save();

            if ($contract->payment && $contract->payment->status === 'HELD') {
                $payment = $contract->payment;
                $payment->status = 'RELEASED';
                $payment->save();

                $wallet = \App\Models\Wallet::where('walletID', $payment->walletID)->first();
                if ($wallet) {
                    $wallet->balance += $payment->amount;
                    $wallet->save();
                }
            }

            Notification::create([
                'notificationID' => Str::uuid(),
                'userID' => $contract->jobSeekerID,
                'title' => 'Pekerjaan Selesai & Dana Cair',
                'message' => 'Employer telah menyetujui pekerjaan "' . $contract->job->title . '". Dana telah diteruskan ke dompet Anda.',
            ]);

            return redirect()->route('reviews.create', $contractId)->with('success', 'Pekerjaan dikonfirmasi selesai dan dana berhasil dicairkan! Silakan berikan ulasan Anda.');

        } elseif ($contract->status === 'WAITING_REVIEW' && $action === 'REVISION') {
            $request->validate([
                'revision_notes' => 'required|string|max:2000',
            ], [
                'revision_notes.required' => 'Catatan revisi wajib diisi.',
            ]);

            $contract->status = 'ACTIVE';
            $contract->revision_notes = $request->input('revision_notes');
            $contract->proof_of_work = null; // Clear proof so seeker must resubmit
            $contract->save();

            Notification::create([
                'notificationID' => Str::uuid(),
                'userID' => $contract->jobSeekerID,
                'title' => 'Revisi Diminta',
                'message' => 'Employer meminta revisi untuk pekerjaan "' . $contract->job->title . '". Catatan: ' . $request->input('revision_notes'),
            ]);

            return back()->with('success', 'Permintaan revisi telah dikirim ke freelancer.');
        }

        return back();
    }
}