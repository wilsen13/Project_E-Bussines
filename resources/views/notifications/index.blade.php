@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ activeTab: 'general' }">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Aktivitas Anda</h1>
            <p class="text-gray-500 mt-1">Pantau semua notifikasi dan status lamaran kerja Anda</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="bg-white p-1 rounded-xl shadow-sm border border-gray-100 mb-6 inline-flex w-full sm:w-auto overflow-x-auto">
        <button @click="activeTab = 'general'" 
                :class="{ 'bg-blue-50 text-blue-600 shadow-sm': activeTab === 'general', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'general' }"
                class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all whitespace-nowrap flex items-center justify-center gap-2">
            <i class="far fa-bell"></i> Notifikasi
        </button>
        
        @if(auth()->user()->role === 'JOB_SEEKER')
        <button @click="activeTab = 'applications'" 
                :class="{ 'bg-blue-50 text-blue-600 shadow-sm': activeTab === 'applications', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'applications' }"
                class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all whitespace-nowrap flex items-center justify-center gap-2">
            <i class="fas fa-clipboard-list"></i> Lamaran
        </button>
        @endif

        <button @click="activeTab = 'contracts'" 
                :class="{ 'bg-blue-50 text-blue-600 shadow-sm': activeTab === 'contracts', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'contracts' }"
                class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all whitespace-nowrap flex items-center justify-center gap-2">
            <i class="fas fa-handshake"></i> Pekerjaan Aktif
        </button>
    </div>

    <!-- Tab Content: General Notifications -->
    <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
        @if($notifications->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 text-2xl">
                    <i class="far fa-bell-slash"></i>
                </div>
                <h3 class="text-gray-900 font-bold mb-1">Belum ada notifikasi</h3>
                <p class="text-sm text-gray-500">Anda akan menerima pemberitahuan di sini ketika ada aktivitas baru.</p>
            </div>
        @else
            @foreach($notifications as $notif)
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:border-blue-100 hover:shadow-md transition-all flex gap-4">
                <div class="shrink-0 w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-info-circle text-lg"></i>
                </div>
                <div class="flex-grow">
                    <div class="flex justify-between items-start mb-1">
                        <h4 class="font-bold text-gray-900">{{ $notif->title }}</h4>
                        <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-md">{{ \Carbon\Carbon::parse($notif->sentAt)->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $notif->message }}</p>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <!-- Tab Content: Application Status -->
    @if(auth()->user()->role === 'JOB_SEEKER' || auth()->user()->role === 'EMPLOYER')
    <div x-show="activeTab === 'applications'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
        @if(count($applications) == 0)
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-400 text-2xl">
                    <i class="fas fa-file-signature"></i>
                </div>
                <h3 class="text-gray-900 font-bold mb-1">Belum ada lamaran</h3>
                <p class="text-sm text-gray-500 mb-6">Mulai eksplorasi peluang kerja dan kirimkan lamaran pertama Anda.</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-blue-600 font-bold hover:text-blue-700 transition-colors">
                    Cari Pekerjaan <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @else
            @foreach($applications as $app)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-blue-100 hover:shadow-md transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 bg-gray-50 border border-gray-200 text-gray-500 rounded-xl flex items-center justify-center font-bold text-lg">
                        {{ auth()->user()->role === 'JOB_SEEKER' ? substr($app->job->employer->displayName ?? 'U', 0, 1) : substr($app->jobSeeker->user->fullName ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-0.5"><a href="{{ route('jobs.show', $app->jobID) }}" class="hover:text-blue-600 transition-colors">{{ $app->job->title }}</a></h4>
                        <p class="text-sm text-gray-500 flex flex-wrap items-center gap-2 mb-2">
                            @if(auth()->user()->role === 'JOB_SEEKER')
                                <span><i class="fas fa-building opacity-70"></i> {{ $app->job->employer->displayName ?? 'Anonim' }}</span>
                            @else
                                <span><i class="fas fa-user opacity-70"></i> Pelamar: {{ $app->jobSeeker->user->fullName ?? 'Anonim' }}</span>
                            @endif
                            <span class="text-gray-300 hidden sm:inline">•</span>
                            <span>Melamar pada {{ date('d M Y', strtotime($app->createdAt)) }}</span>
                        </p>
                        @if(auth()->user()->role === 'EMPLOYER')
                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700 mt-2">
                                <span class="font-semibold block mb-1">Pesan Lamaran:</span>
                                {{ $app->letter }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="shrink-0 w-full sm:w-auto mt-4 sm:mt-0">
                    @if(auth()->user()->role === 'JOB_SEEKER')
                        @if($app->status === 'APPLIED')
                            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span> Terkirim
                            </span>
                        @elseif($app->status === 'REJECTED')
                            <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 border border-red-200 px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                <i class="fas fa-times"></i> Ditolak
                            </span>
                        @elseif($app->status === 'SHORTLISTED')
                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                <i class="fas fa-clock"></i> Diproses
                            </span>
                        @elseif($app->status === 'HIRED')
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                <i class="fas fa-check-double"></i> Diterima
                            </span>
                        @endif
                    @elseif(auth()->user()->role === 'EMPLOYER' && $app->status === 'APPLIED')
                        <form action="{{ route('applications.process', $app->applicationID) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <button type="submit" name="action" value="APPROVE" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors" onclick="return confirm('Terima lamaran ini? Pekerjaan akan ditutup dan pembayaran akan ditahan (Escrow).')">
                                <i class="fas fa-check"></i> Terima
                            </button>
                            <button type="submit" name="action" value="REJECT" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors" onclick="return confirm('Tolak lamaran ini?')">
                                Tolak
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        @endif
    </div>
    @endif

    <!-- Tab Content: Contracts (Active Jobs) -->
    <div x-show="activeTab === 'contracts'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
        @if(count($contracts) == 0)
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-400 text-2xl">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3 class="text-gray-900 font-bold mb-1">Belum ada kontrak aktif</h3>
                <p class="text-sm text-gray-500">Pekerjaan yang telah disetujui akan muncul di sini.</p>
            </div>
        @else
            @foreach($contracts as $contract)
            <div class="bg-white p-6 rounded-2xl shadow-sm border {{ $contract->status === 'WAITING_REVIEW' ? 'border-amber-300 bg-amber-50/30' : 'border-gray-100' }} hover:shadow-md transition-all flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-bold text-lg">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">{{ $contract->job->title ?? 'Pekerjaan' }}</h4>
                        <div class="text-sm text-gray-600 mb-2 space-y-1">
                            @if(auth()->user()->role === 'JOB_SEEKER')
                                <p><i class="fas fa-building text-gray-400 w-4"></i> Pemberi Kerja: <span class="font-medium">{{ $contract->job->employer->displayName ?? 'Anonim' }}</span></p>
                            @else
                                <p><i class="fas fa-user text-gray-400 w-4"></i> Freelancer: <span class="font-medium">{{ $contract->jobSeeker->user->fullName ?? 'Anonim' }}</span></p>
                            @endif
                            <p><i class="fas fa-money-bill-wave text-gray-400 w-4"></i> Nilai Kontrak: <span class="font-bold text-green-600">Rp {{ number_format($contract->payment->amount ?? 0, 0, ',', '.') }}</span></p>
                            <p><i class="fas fa-info-circle text-gray-400 w-4"></i> Status: 
                                @if($contract->status === 'ACTIVE')
                                    <span class="text-blue-600 font-bold">Sedang Dikerjakan</span>
                                @elseif($contract->status === 'WAITING_REVIEW')
                                    <span class="text-amber-600 font-bold">Menunggu Ulasan Employer</span>
                                @elseif($contract->status === 'COMPLETED')
                                    <span class="text-green-600 font-bold">Selesai</span>
                                @else
                                    <span class="text-red-600 font-bold">Dibatalkan</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="shrink-0 w-full md:w-auto">
                    @if(auth()->user()->role === 'JOB_SEEKER' && $contract->status === 'ACTIVE')
                        <form action="{{ route('contracts.complete', $contract->contractID) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition-colors text-sm">
                                <i class="fas fa-check mr-1"></i> Tandai Pekerjaan Selesai
                            </button>
                        </form>
                    @elseif(auth()->user()->role === 'EMPLOYER' && $contract->status === 'WAITING_REVIEW')
                        <form action="{{ route('contracts.confirm', $contract->contractID) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition-colors text-sm">
                                <i class="fas fa-check-double mr-1"></i> Konfirmasi Selesai & Cairkan Dana
                            </button>
                        </form>
                    @elseif($contract->status === 'COMPLETED')
                        @if(!in_array($contract->contractID, $reviewedContracts ?? []))
                            <a href="{{ route('reviews.create', $contract->contractID) }}" class="w-full inline-flex justify-center items-center bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition-colors text-sm">
                                <i class="fas fa-star mr-1"></i> Beri Ulasan
                            </a>
                        @else
                            <span class="w-full inline-block text-center bg-gray-100 text-gray-600 font-bold py-2.5 px-5 rounded-xl shadow-sm text-sm">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i> Selesai
                            </span>
                        @endif
                    @endif
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