<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employer;
use App\Models\JobSeeker;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegister() { return view('auth.register'); }
    
    public function register(Request $request) {
        $request->validate([
            'fullName' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:10',
            'password' => 'required|min:8',
            'role' => 'required|in:EMPLOYER,JOB_SEEKER'
        ]);

        $user = User::create([
            'userID' => Str::uuid(),
            'fullName' => $request->fullName,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($user->role === 'EMPLOYER') {
            Employer::create(['employerID' => $user->userID, 'displayName' => $user->fullName]);
        } else {
            JobSeeker::create(['jobSeekerID' => $user->userID]);
        }
        
        Wallet::create(['walletID' => Str::uuid(), 'userID' => $user->userID]);

        Auth::login($user);
        return redirect()->route('home');
    }

    public function showLogin() { return view('auth.login'); }

    public function login(Request $request) {
        $credentials = $request->validate(['username' => 'required', 'password' => 'required']);
        
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }
        
        if (Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->withErrors(['username' => 'Kredensial tidak valid.']);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}