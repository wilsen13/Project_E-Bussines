<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ContractController;

Route::get('/', [JobController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/{id}/edit', [JobController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{id}', [JobController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{id}', [JobController::class, 'destroy'])->name('jobs.destroy');
    Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{id}/apply', [ApplicationController::class, 'store'])->name('jobs.apply');
    
    Route::get('/notifications', [ApplicationController::class, 'notifications'])->name('notifications.index');
    Route::post('/applications/{id}/process', [ApplicationController::class, 'process'])->name('applications.process');
    Route::post('/contracts/{id}/complete', [ApplicationController::class, 'markComplete'])->name('contracts.complete');
    Route::post('/contracts/{id}/confirm', [ApplicationController::class, 'confirmComplete'])->name('contracts.confirm');
    
    // Dedicated Contract Detail Page
    Route::get('/contracts/{id}', [ContractController::class, 'show'])->name('contracts.show');
    Route::post('/contracts/{id}/submit-proof', [ContractController::class, 'submitProof'])->name('contracts.submitProof');
    Route::post('/contracts/{id}/confirm-detail', [ContractController::class, 'confirmComplete'])->name('contracts.confirmFromDetail');
    
    Route::get('/contracts/{id}/review', [\App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/contracts/{id}/review', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // Profile Routes
    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Wallet Routes
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/release/{payment}', [WalletController::class, 'releasePayment'])->name('wallet.release');
});
