<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function show($id)
    {
        $user = \App\Models\User::where('userID', $id)->firstOrFail();
        
        // Load role-specific data
        if ($user->role === 'JOB_SEEKER') {
            $user->load('jobSeeker');
        } elseif ($user->role === 'EMPLOYER') {
            $user->load('employer');
        }

        // Get reviews about this user
        $reviews = \App\Models\Review::with(['reviewer', 'contract.job'])
            ->where('revieweeUserID', $id)
            ->latest('createdAt')
            ->get();

        // Count completed contracts
        $completedContracts = \App\Models\Contract::where('status', 'COMPLETED')
            ->where(function($q) use ($id) {
                $q->where('jobSeekerID', $id)->orWhere('employerID', $id);
            })->count();

        return view('profile.show', compact('user', 'reviews', 'completedContracts'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|min:10',
            'bio' => 'nullable|string',
        ]);

        $user = Auth::user();
        $user->fullName = $request->fullName;
        $user->phone = $request->phone;
        $user->bio = $request->bio;
        
        // Also update displayName if Employer
        if ($user->role === 'EMPLOYER' && $user->employer) {
            $user->employer->displayName = $request->fullName;
            $user->employer->save();
        }

        $user->save();

        return back()->with('success_profile', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.'])->with('error_password', true);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success_password', 'Password berhasil diubah!');
    }
}
