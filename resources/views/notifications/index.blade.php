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

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-700 font-medium">{{ $error }}</p>
                    @endforeach
                </div>
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
        
        <button @click="activeTab = 'applications'" 
                :class="{ 'bg-blue-50 text-blue-600 shadow-sm': activeTab === 'applications', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'applications' }"
                class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all whitespace-nowrap flex items-center justify-center gap-2">
            <i class="fas fa-clipboard-list"></i> 
            @if(auth()->user()->role === 'EMPLOYER')
                Lamaran Masuk
            @else
                Lamaran
            @endif
        </button>

        <button @click="activeTab = 'contracts'" 
                :class="{ 'bg-blue-50 text-blue-600 shadow-sm': activeTab === 'contracts', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'contracts' }"
                class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all whitespace-nowrap flex items-center justify-center gap-2">
            <i class="fas fa-handshake"></i> Pekerjaan Aktif
        </button>
    </div>

    {{-- ================================================================ --}}
    {{-- TAB 1: GENERAL NOTIFICATIONS — Color-coded by title keywords     --}}
    {{-- ================================================================ --}}
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
                @php
                    $titleLower = strtolower($notif->title);
                    // Determine color coding based on notification title
                    if (str_contains($titleLower, 'diterima') || str_contains($titleLower, 'selesai') || str_contains($titleLower, 'dana cair') || str_contains($titleLower, 'berhasil')) {
                        $notifBg = 'bg-green-50 border-l-4 border-green-500';
                        $notifIconBg = 'bg-green-100 text-green-600';
                        $notifIcon = 'fa-check-circle';
                        $notifTitleColor = 'text-green-800';
                        $notifTextColor = 'text-green-700';
                    } elseif (str_contains($titleLower, 'ditolak') || str_contains($titleLower, 'dibatalkan')) {
                        $notifBg = 'bg-red-50 border-l-4 border-red-500';
                        $notifIconBg = 'bg-red-100 text-red-600';
                        $notifIcon = 'fa-times-circle';
                        $notifTitleColor = 'text-red-800';
                        $notifTextColor = 'text-red-700';
                    } elseif (str_contains($titleLower, 'revisi') || str_contains($titleLower, 'menunggu') || str_contains($titleLower, 'bukti')) {
                        $notifBg = 'bg-amber-50 border-l-4 border-amber-400';
                        $notifIconBg = 'bg-amber-100 text-amber-600';
                        $notifIcon = 'fa-exclamation-triangle';
                        $notifTitleColor = 'text-amber-800';
                        $notifTextColor = 'text-amber-700';
                    } elseif (str_contains($titleLower, 'terkirim') || str_contains($titleLower, 'lamaran masuk') || str_contains($titleLower, 'baru')) {
                        $notifBg = 'bg-blue-50 border-l-4 border-blue-400';
                        $notifIconBg = 'bg-blue-100 text-blue-600';
                        $notifIcon = 'fa-paper-plane';
                        $notifTitleColor = 'text-blue-800';
                        $notifTextColor = 'text-blue-700';
                    } else {
                        $notifBg = 'bg-white border border-gray-100';
                        $notifIconBg = 'bg-gray-100 text-gray-500';
                        $notifIcon = 'fa-info-circle';
                        $notifTitleColor = 'text-gray-900';
                        $notifTextColor = 'text-gray-600';
                    }
                @endphp
                <div class="{{ $notifBg }} p-5 rounded-2xl shadow-sm hover:shadow-md transition-all flex gap-4">
                    <div class="shrink-0 w-11 h-11 {{ $notifIconBg }} rounded-full flex items-center justify-center">
                        <i class="fas {{ $notifIcon }} text-base"></i>
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="flex justify-between items-start gap-2 mb-1">
                            <h4 class="font-bold {{ $notifTitleColor }} text-sm">{{ $notif->title }}</h4>
                            <span class="text-xs font-medium text-gray-400 bg-white/70 px-2 py-0.5 rounded-md shrink-0">{{ \Carbon\Carbon::parse($notif->sentAt)->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm {{ $notifTextColor }} leading-relaxed">{{ $notif->message }}</p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- ================================================================ --}}
    {{-- TAB 2: APPLICATIONS — Overhauled for Employer with profile data  --}}
    {{-- ================================================================ --}}
    <div x-show="activeTab === 'applications'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
        @if(count($applications) == 0)
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-400 text-2xl">
                    <i class="fas fa-file-signature"></i>
                </div>
                @if(auth()->user()->role === 'EMPLOYER')
                    <h3 class="text-gray-900 font-bold mb-1">Belum ada lamaran masuk</h3>
                    <p class="text-sm text-gray-500 mb-6">Lamaran dari pencari kerja akan muncul di sini saat ada yang melamar pekerjaan Anda.</p>
                @else
                    <h3 class="text-gray-900 font-bold mb-1">Belum ada lamaran</h3>
                    <p class="text-sm text-gray-500 mb-6">Mulai eksplorasi peluang kerja dan kirimkan lamaran pertama Anda.</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-blue-600 font-bold hover:text-blue-700 transition-colors">
                        Cari Pekerjaan <i class="fas fa-arrow-right"></i>
                    </a>
                @endif
            </div>
        @else
            @foreach($applications as $app)
                {{-- ===== EMPLOYER VIEW: Rich applicant profile card ===== --}}
                @if(auth()->user()->role === 'EMPLOYER')
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all overflow-hidden">
                        {{-- Card Header: Job info --}}
                        <div class="bg-gray-50 px-6 py-3 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <i class="fas fa-briefcase text-blue-500"></i>
                                <a href="{{ route('jobs.show', $app->jobID) }}" class="font-semibold text-gray-700 hover:text-blue-600 transition-colors">{{ $app->job->title }}</a>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">{{ date('d M Y', strtotime($app->createdAt)) }}</span>
                        </div>

                        <div class="p-6">
                            {{-- Applicant Profile Section --}}
                            <div class="flex items-start gap-5">
                                <img class="w-16 h-16 rounded-2xl object-cover border-2 border-gray-100 shadow-sm shrink-0" 
                                     src="https://ui-avatars.com/api/?name={{ urlencode($app->jobSeeker->user->fullName ?? 'U') }}&background=EBF4FF&color=2563EB&bold=true&size=128" 
                                     alt="{{ $app->jobSeeker->user->fullName ?? 'Anonim' }}">
                                <div class="flex-grow min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <h4 class="font-extrabold text-gray-900 text-lg">{{ $app->jobSeeker->user->fullName ?? 'Anonim' }}</h4>
                                        @if($app->jobSeeker->user->rating)
                                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-600 border border-amber-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                                <i class="fas fa-star text-amber-400"></i> {{ number_format($app->jobSeeker->user->rating, 1) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-gray-50 text-gray-400 border border-gray-200 px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                <i class="far fa-star"></i> Belum ada rating
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Bio & Education snippets --}}
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 mt-1">
                                        @if($app->jobSeeker->education)
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fas fa-graduation-cap text-gray-400"></i> {{ $app->jobSeeker->education }}
                                            </span>
                                        @endif
                                        @if($app->jobSeeker->user->email)
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fas fa-envelope text-gray-400"></i> {{ $app->jobSeeker->user->email }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($app->jobSeeker->user->bio)
                                        <p class="text-sm text-gray-600 mt-2 line-clamp-2 leading-relaxed">{{ $app->jobSeeker->user->bio }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Application Message --}}
                            <div class="mt-4 bg-blue-50/50 border border-blue-100 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <i class="fas fa-comment-dots text-blue-400 text-sm"></i>
                                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Pesan Lamaran</span>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $app->letter ?? '-' }}</p>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-5 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                <form action="{{ route('applications.process', $app->applicationID) }}" method="POST" class="flex items-center gap-2 flex-grow">
                                    @csrf
                                    <button type="submit" name="action" value="APPROVE" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all inline-flex items-center justify-center gap-2" onclick="return confirm('Terima lamaran ini? Pekerjaan akan ditutup dan pembayaran akan ditahan (Escrow).')">
                                        <i class="fas fa-check-circle"></i> Terima
                                    </button>
                                    <button type="submit" name="action" value="REJECT" class="flex-1 bg-white hover:bg-red-50 text-red-600 border border-red-200 px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all inline-flex items-center justify-center gap-2" onclick="return confirm('Tolak lamaran ini?')">
                                        <i class="fas fa-times-circle"></i> Tolak
                                    </button>
                                </form>
                                <a href="{{ route('profile.show', $app->jobSeeker->user->userID ?? '') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-bold transition-all inline-flex items-center justify-center gap-2 shrink-0">
                                    <i class="fas fa-user-circle"></i> Lihat Profil Lengkap
                                </a>
                            </div>
                        </div>
                    </div>

                {{-- ===== JOB SEEKER VIEW: Application status ===== --}}
                @else
                    @php
                        if ($app->status === 'APPLIED') {
                            $statusBorder = 'border-blue-200';
                            $statusBg = 'bg-blue-50';
                        } elseif ($app->status === 'HIRED') {
                            $statusBorder = 'border-green-200';
                            $statusBg = 'bg-green-50/30';
                        } elseif ($app->status === 'REJECTED') {
                            $statusBorder = 'border-red-200';
                            $statusBg = 'bg-red-50/30';
                        } else {
                            $statusBorder = 'border-gray-100';
                            $statusBg = '';
                        }
                    @endphp
                    <div class="bg-white p-6 rounded-2xl shadow-sm border {{ $statusBorder }} {{ $statusBg }} hover:shadow-md transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-12 h-12 bg-gray-50 border border-gray-200 text-gray-500 rounded-xl flex items-center justify-center font-bold text-lg">
                                {{ substr($app->job->employer->displayName ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-0.5"><a href="{{ route('jobs.show', $app->jobID) }}" class="hover:text-blue-600 transition-colors">{{ $app->job->title }}</a></h4>
                                <p class="text-sm text-gray-500 flex flex-wrap items-center gap-2 mb-2">
                                    <span><i class="fas fa-building opacity-70"></i> {{ $app->job->employer->displayName ?? 'Anonim' }}</span>
                                    <span class="text-gray-300 hidden sm:inline">•</span>
                                    <span>Melamar pada {{ date('d M Y', strtotime($app->createdAt)) }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="shrink-0">
                            @if($app->status === 'APPLIED')
                                <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span> Terkirim
                                </span>
                            @elseif($app->status === 'REJECTED')
                                <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 border border-red-200 px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                    <i class="fas fa-times-circle"></i> Ditolak
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
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    {{-- ================================================================ --}}
    {{-- TAB 3: CONTRACTS — Simplified with link to detail page           --}}
    {{-- ================================================================ --}}
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
            <div class="bg-white rounded-2xl shadow-sm border {{ $contract->status === 'WAITING_REVIEW' ? 'border-amber-300 bg-amber-50/30' : ($contract->status === 'COMPLETED' ? 'border-green-200 bg-green-50/20' : 'border-gray-100') }} hover:shadow-md transition-all overflow-hidden">
                <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 w-12 h-12 {{ $contract->status === 'COMPLETED' ? 'bg-green-50 text-green-600' : ($contract->status === 'WAITING_REVIEW' ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600') }} rounded-xl flex items-center justify-center font-bold text-lg">
                            <i class="fas {{ $contract->status === 'COMPLETED' ? 'fa-check-circle' : ($contract->status === 'WAITING_REVIEW' ? 'fa-hourglass-half' : 'fa-briefcase') }}"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">{{ $contract->job->title ?? 'Pekerjaan' }}</h4>
                            <div class="text-sm text-gray-600 mb-2 space-y-0.5">
                                @if(auth()->user()->role === 'JOB_SEEKER')
                                    <p><i class="fas fa-building text-gray-400 w-4"></i> {{ $contract->job->employer->displayName ?? 'Anonim' }}</p>
                                @else
                                    <p><i class="fas fa-user text-gray-400 w-4"></i> {{ $contract->jobSeeker->user->fullName ?? 'Anonim' }}</p>
                                @endif
                                <p><i class="fas fa-money-bill-wave text-gray-400 w-4"></i> <span class="font-bold text-green-600">Rp {{ number_format($contract->payment->amount ?? 0, 0, ',', '.') }}</span></p>
                            </div>
                            {{-- Status Badge --}}
                            @if($contract->status === 'ACTIVE')
                                <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Sedang Dikerjakan
                                </span>
                            @elseif($contract->status === 'WAITING_REVIEW')
                                <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-hourglass-half text-[10px]"></i> Menunggu Review
                                </span>
                            @elseif($contract->status === 'COMPLETED')
                                <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-check-double text-[10px]"></i> Selesai
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('contracts.show', $contract->contractID) }}" class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all inline-flex items-center gap-2">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </a>
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