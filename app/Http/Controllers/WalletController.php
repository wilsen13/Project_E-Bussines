<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\Contract;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = Wallet::where('userID', $user->userID)->first();
        
        // If wallet doesn't exist for some reason, create it
        if (!$wallet) {
            $wallet = Wallet::create([
                'walletID' => \Illuminate\Support\Str::uuid(),
                'userID' => $user->userID,
                'balance' => 0
            ]);
        }

        // Get held payments (escrow) associated with this wallet
        $heldPayments = Payment::with(['contract.job', 'contract.employer.user'])
            ->where('walletID', $wallet->walletID)
            ->where('status', 'HELD')
            ->latest('createdAt')
            ->get();
            
        // Get released payments (history)
        $releasedPayments = Payment::with(['contract.job', 'contract.employer.user'])
            ->where('walletID', $wallet->walletID)
            ->where('status', 'RELEASED')
            ->latest('createdAt')
            ->get();

        return view('wallet.index', compact('wallet', 'heldPayments', 'releasedPayments'));
    }

    public function releasePayment(Request $request, $paymentId)
    {
        $user = Auth::user();
        $wallet = Wallet::where('userID', $user->userID)->first();
        
        $payment = Payment::where('paymentID', $paymentId)
            ->where('walletID', $wallet->walletID)
            ->where('status', 'HELD')
            ->firstOrFail();

        // Simulate completing the job: Update payment to RELEASED
        $payment->status = 'RELEASED';
        $payment->save();

        // Update Wallet Balance
        $wallet->balance += $payment->amount;
        $wallet->save();
        
        // Also mark contract as completed
        $contract = Contract::find($payment->contractID);
        if ($contract) {
            $contract->status = 'COMPLETED';
            $contract->endAt = now();
            $contract->save();
        }

        return back()->with('success', 'Simulasi berhasil: Pekerjaan diselesaikan dan dana sebesar Rp ' . number_format($payment->amount, 0, ',', '.') . ' telah ditambahkan ke saldo Anda.');
    }
}
