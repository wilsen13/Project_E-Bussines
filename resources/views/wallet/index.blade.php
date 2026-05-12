@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ activeTab: 'escrow' }">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dompet & Pembayaran</h1>
        <p class="text-gray-500 mt-1">Kelola saldo dan pantau pembayaran pekerjaan Anda</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-8 shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Wallet Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <!-- Main Balance -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-3xl p-8 text-white shadow-lg shadow-blue-600/30 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative z-10">
                <p class="text-blue-100 font-medium mb-1 flex items-center gap-2"><i class="fas fa-wallet"></i> Saldo Aktif</p>
                <h2 class="text-4xl font-black mb-6">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</h2>
                <div class="flex gap-3">
                    <button class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded-xl text-sm font-bold transition-colors">Tarik Dana</button>
                    <button class="bg-blue-800/50 hover:bg-blue-800/70 backdrop-blur-sm text-white px-4 py-2 rounded-xl text-sm font-bold transition-colors">Riwayat</button>
                </div>
            </div>
        </div>

        <!-- Held Balance (Escrow) -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden">
            @php
                $totalHeld = $heldPayments->sum('amount');
            @endphp
            <div class="relative z-10">
                <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex justify-center items-center mb-4 text-xl">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">Saldo Ditahan (Escrow)</p>
                <h2 class="text-3xl font-black text-gray-900 mb-2">Rp {{ number_format($totalHeld, 0, ',', '.') }}</h2>
                <p class="text-sm text-gray-400 leading-relaxed">Dana ini ditahan sementara dengan aman sampai pekerjaan diselesaikan dan disetujui oleh kedua belah pihak.</p>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white p-1 rounded-xl shadow-sm border border-gray-100 mb-6 inline-flex w-full sm:w-auto overflow-x-auto">
        <button @click="activeTab = 'escrow'" 
                :class="{ 'bg-blue-50 text-blue-600 shadow-sm': activeTab === 'escrow', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'escrow' }"
                class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all whitespace-nowrap flex items-center justify-center gap-2">
            <i class="fas fa-lock"></i> Pembayaran Dalam Proses ({{ $heldPayments->count() }})
        </button>
        <button @click="activeTab = 'history'" 
                :class="{ 'bg-blue-50 text-blue-600 shadow-sm': activeTab === 'history', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'history' }"
                class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all whitespace-nowrap flex items-center justify-center gap-2">
            <i class="fas fa-history"></i> Riwayat Pencairan
        </button>
    </div>

    <!-- Tab: Escrow -->
    <div x-show="activeTab === 'escrow'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
        @if($heldPayments->isEmpty())
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-12 text-center">
                <i class="fas fa-check-circle text-gray-300 text-4xl mb-3"></i>
                <h3 class="font-bold text-gray-900 mb-1">Tidak ada dana yang ditahan</h3>
                <p class="text-sm text-gray-500">Semua pekerjaan Anda telah selesai atau Anda belum memulai pekerjaan baru.</p>
            </div>
        @else
            @foreach($heldPayments as $payment)
            <div class="bg-white border border-amber-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 px-2.5 py-1 rounded-md text-xs font-bold mb-3 border border-amber-200">
                        <i class="fas fa-clock"></i> Tertahan (Sedang Dikerjakan)
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">{{ $payment->contract->job->title ?? 'Pekerjaan Tidak Diketahui' }}</h3>
                    <p class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                        <i class="fas fa-building opacity-70"></i> {{ $payment->contract->employer->displayName ?? 'Pemberi Kerja' }}
                    </p>
                </div>
                
                <div class="flex flex-col md:items-end gap-3 w-full md:w-auto">
                    <div class="text-left md:text-right">
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Jumlah Dana</p>
                        <p class="text-xl font-black text-amber-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </div>
                    
                    <form action="{{ route('wallet.release', $payment->paymentID) }}" method="POST" class="w-full md:w-auto">
                        @csrf
                        <button type="submit" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i> Simulasi: Selesaikan Pekerjaan
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <!-- Tab: History -->
    <div x-show="activeTab === 'history'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
        @if($releasedPayments->isEmpty())
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-12 text-center">
                <i class="fas fa-receipt text-gray-300 text-4xl mb-3"></i>
                <h3 class="font-bold text-gray-900 mb-1">Belum ada riwayat</h3>
                <p class="text-sm text-gray-500">Anda belum memiliki riwayat dana yang dicairkan.</p>
            </div>
        @else
            @foreach($releasedPayments as $payment)
            <div class="bg-white border border-gray-100 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center shrink-0">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $payment->contract->job->title ?? 'Pekerjaan' }}</h4>
                        <p class="text-xs text-gray-500">{{ date('d M Y, H:i', strtotime($payment->updatedAt ?? $payment->createdAt)) }}</p>
                    </div>
                </div>
                <div class="text-right ml-14 sm:ml-0">
                    <p class="font-black text-green-600">+ Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    <p class="text-xs font-bold text-gray-400">Dicairkan</p>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
