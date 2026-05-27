<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    public function show($id)
    {
        $user = Auth::user();
        $contract = Contract::with([
            'job.employer.user',
            'job.location',
            'job.category',
            'jobSeeker.user',
            'payment'
        ])->where('contractID', $id)->firstOrFail();

        // Only allow the employer or seeker involved in this contract to view
        if ($user->userID !== $contract->employerID && $user->userID !== $contract->jobSeekerID) {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke kontrak ini.');
        }

        return view('contracts.show', compact('contract'));
    }

    public function submitProof(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'JOB_SEEKER') {
            return back()->with('error', 'Hanya pencari kerja yang dapat mengirim bukti.');
        }

        $contract = Contract::with('job')->where('contractID', $id)->where('jobSeekerID', $user->userID)->firstOrFail();

        if ($contract->status !== 'ACTIVE') {
            return back()->with('error', 'Kontrak ini tidak dalam status aktif.');
        }

        $request->validate([
            'proof_of_work' => 'required|string|max:2000',
            'proof_file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,zip,rar',
        ], [
            'proof_of_work.required' => 'Deskripsi bukti pekerjaan wajib diisi.',
            'proof_file.max' => 'Ukuran file maksimal 10MB.',
            'proof_file.mimes' => 'Format file yang diizinkan: jpg, png, gif, pdf, doc, docx, zip, rar.',
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('proofs', $fileName, 'public');
        }

        $contract->status = 'WAITING_REVIEW';
        $contract->proof_of_work = $request->input('proof_of_work');
        $contract->proof_file_path = $filePath;
        $contract->revision_notes = null;
        $contract->save();

        Notification::create([
            'notificationID' => Str::uuid(),
            'userID' => $contract->employerID,
            'title' => 'Bukti Pekerjaan Dikirim',
            'message' => 'Freelancer telah mengirimkan bukti pekerjaan untuk "' . $contract->job->title . '". Silakan periksa dan setujui atau minta revisi.',
        ]);

        return redirect()->route('contracts.show', $contract->contractID)
            ->with('success', 'Hasil pekerjaan berhasil di kirim, silahkan menunggu konfirmasi dari klien/pemberi kerja');
    }

    public function confirmComplete(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'EMPLOYER') return back();

        $contract = Contract::with(['payment', 'job'])->where('contractID', $id)->where('employerID', $user->userID)->firstOrFail();

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

            return redirect()->route('reviews.create', $id)->with('success', 'Pekerjaan dikonfirmasi selesai dan dana berhasil dicairkan! Silakan berikan ulasan Anda.');

        } elseif ($contract->status === 'WAITING_REVIEW' && $action === 'REVISION') {
            $request->validate([
                'revision_notes' => 'required|string|max:2000',
            ], [
                'revision_notes.required' => 'Catatan revisi wajib diisi.',
            ]);

            $contract->status = 'ACTIVE';
            $contract->revision_notes = $request->input('revision_notes');
            $contract->proof_of_work = null;
            $contract->proof_file_path = null;
            $contract->save();

            Notification::create([
                'notificationID' => Str::uuid(),
                'userID' => $contract->jobSeekerID,
                'title' => 'Revisi Diminta',
                'message' => 'Employer meminta revisi untuk pekerjaan "' . $contract->job->title . '". Catatan: ' . $request->input('revision_notes'),
            ]);

            return redirect()->route('contracts.show', $id)->with('success', 'Permintaan revisi telah dikirim ke freelancer.');
        }

        return back();
    }
}
