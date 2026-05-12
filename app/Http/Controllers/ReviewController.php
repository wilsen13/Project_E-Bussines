<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function create($contractId)
    {
        $user = Auth::user();
        $contract = Contract::with(['job', 'jobSeeker.user', 'employer.user'])->where('contractID', $contractId)->firstOrFail();

        // Ensure user is part of the contract and it's COMPLETED
        if ($contract->status !== 'COMPLETED') {
            return redirect()->route('notifications.index')->with('error', 'Kontrak belum selesai.');
        }

        if ($user->userID !== $contract->employerID && $user->userID !== $contract->jobSeekerID) {
            return redirect()->route('home');
        }

        // Check if already reviewed
        $existingReview = Review::where('contractID', $contractId)
            ->where('reviewerUserID', $user->userID)
            ->first();

        if ($existingReview) {
            return redirect()->route('notifications.index')->with('success', 'Anda sudah memberikan ulasan untuk pekerjaan ini.');
        }

        $targetUser = $user->userID === $contract->employerID ? $contract->jobSeeker->user : $contract->employer->user;

        return view('reviews.create', compact('contract', 'targetUser'));
    }

    public function store(Request $request, $contractId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $contract = Contract::where('contractID', $contractId)->firstOrFail();

        if ($contract->status !== 'COMPLETED') return back();

        if ($user->userID !== $contract->employerID && $user->userID !== $contract->jobSeekerID) return back();

        // Prevent duplicate reviews
        if (Review::where('contractID', $contractId)->where('reviewerUserID', $user->userID)->exists()) {
            return redirect()->route('notifications.index');
        }

        $targetUserId = $user->userID === $contract->employerID ? $contract->jobSeekerID : $contract->employerID;

        Review::create([
            'reviewID' => Str::uuid(),
            'contractID' => $contractId,
            'reviewerUserID' => $user->userID,
            'revieweeUserID' => $targetUserId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Calculate and update the new average rating for the target user
        $targetUser = User::where('userID', $targetUserId)->first();
        if ($targetUser) {
            $average = Review::where('revieweeUserID', $targetUserId)->avg('rating');
            $targetUser->rating = round($average, 2);
            $targetUser->save();
        }

        return redirect()->route('notifications.index')->with('success', 'Terima kasih! Ulasan Anda berhasil disimpan.');
    }
}
