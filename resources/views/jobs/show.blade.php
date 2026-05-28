@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <a href="{{ route('home') }}" class="text-gray-500 hover:text-blue-600 font-medium mb-6 inline-flex items-center gap-2 transition-colors">
        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
    </a>
    
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Header Section -->
        <div class="relative w-full h-64 sm:h-80 bg-gray-900 overflow-hidden">
            @if($job->image_url)
                @if(str_starts_with($job->image_url, 'http'))
                    <img src="{{ $job->image_url }}" alt="Thumbnail" class="w-full h-full object-cover opacity-60">
                @else
                    <img src="{{ asset('storage/' . $job->image_url) }}" alt="Thumbnail" class="w-full h-full object-cover opacity-60">
                @endif
            @else
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-800"></div>
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
            @endif
            <div class="absolute inset-0 flex flex-col md:flex-row md:items-end justify-between gap-6 p-8 sm:p-10 text-white bg-gradient-to-t from-gray-900 to-transparent">
                <div class="w-full max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-500/80 backdrop-blur-md rounded-full text-blue-50 text-xs font-semibold mb-3 border border-blue-400/50 shadow-sm">
                        <i class="fas fa-briefcase"></i> {{ $job->category ? $job->category->name : 'Umum' }}
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold mb-2 leading-tight drop-shadow-md">{{ $job->title }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-gray-200 text-sm font-medium drop-shadow-sm">
                        <span class="flex items-center gap-2 bg-gray-900/40 px-3 py-1.5 rounded-lg backdrop-blur-md"><i class="fas fa-user-circle"></i> {{ $job->employer->displayName ?? 'Anonim' }}</span>
                        @if($job->is_remote)
                            <span class="flex items-center gap-2 bg-blue-600/80 px-3 py-1.5 rounded-lg backdrop-blur-md font-bold text-white"><i class="fas fa-laptop"></i> Kerja Jarak Jauh (Remote)</span>
                        @else
                            <span class="flex items-center gap-2 bg-gray-900/40 px-3 py-1.5 rounded-lg backdrop-blur-md"><i class="fas fa-map-marker-alt text-red-400"></i> {{ $job->location ? $job->location->city : 'Lokasi Fisik' }}</span>
                        @endif
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-6 py-4 rounded-2xl shadow-xl shrink-0 text-center">
                    <p class="text-xs text-blue-100 font-bold uppercase tracking-wider mb-1">Kompensasi</p>
                    <p class="text-2xl font-black drop-shadow-md">Rp {{ number_format($job->payAmount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="p-8 sm:p-10">
            <!-- Description -->
            <div class="mb-10 prose prose-blue max-w-none">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2 border-b border-gray-100 pb-3">
                    <i class="fas fa-align-left text-blue-600"></i> Deskripsi Pekerjaan
                </h3>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $job->description }}</p>
            </div>

            <!-- Expandable Location (AlpineJS) -->
            @if(!$job->is_remote && $job->location)
            <div class="mb-10" x-data="{ expanded: false }">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between bg-gray-50 hover:bg-gray-100 p-4 rounded-xl border border-gray-200 transition-colors focus:outline-none">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-red-100 text-red-500 flex items-center justify-center">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-gray-900">Lokasi Pekerjaan</h4>
                            <p class="text-sm text-gray-500">{{ $job->location->city }}, {{ $job->location->province }}</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="{ 'rotate-180': expanded }"></i>
                </button>
                
                <div x-show="expanded" x-collapse x-cloak class="mt-3 bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex flex-col gap-3 text-sm">
                        <div class="flex gap-3">
                            <i class="fas fa-location-dot mt-1 text-red-500"></i>
                            <div>
                                <p class="font-bold text-gray-900">Alamat Lengkap</p>
                                <p class="text-gray-600">{{ $job->location->addressLine }}</p>
                                <p class="text-gray-600">{{ $job->location->city }}, {{ $job->location->province }} {{ $job->location->postalCode }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Application Form -->
            <div class="mt-10 pt-8 border-t border-gray-200">
                @auth
                    @if(auth()->user()->role === 'JOB_SEEKER')
                        <div class="bg-blue-50 rounded-2xl p-6 md:p-8 border border-blue-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Tertarik dengan pekerjaan ini?</h3>
                            <p class="text-sm text-gray-600 mb-6">Kirimkan lamaran Anda sekarang. Ceritakan mengapa Anda cocok untuk pekerjaan ini.</p>
                            
                            <form action="{{ route('jobs.apply', $job->jobID) }}" method="POST">
                                @csrf
                                <div class="mb-5">
                                    <label for="letter" class="block text-sm font-semibold text-gray-700 mb-2">Pesan Lamaran (Surat Pengantar)</label>
                                    <textarea id="letter" name="letter" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors resize-none placeholder-gray-400" placeholder="Tulis pesan singkat tentang keahlian dan ketersediaan Anda..." required></textarea>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3.5 px-6 rounded-xl hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/30 transition-all duration-300 flex items-center justify-center gap-2">
                                    <i class="fas fa-paper-plane"></i> Kirim Lamaran Sekarang
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center">
                            <i class="fas fa-info-circle text-gray-400 text-3xl mb-3"></i>
                            <h3 class="text-gray-900 font-bold mb-1">Anda masuk sebagai Pemberi Kerja</h3>
                            <p class="text-sm text-gray-500">Hanya akun Pencari Kerja yang dapat melamar pekerjaan.</p>
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center flex flex-col items-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 mb-4 text-blue-600 text-xl">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Masuk untuk Melamar</h3>
                        <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">Anda harus masuk ke akun Pencari Kerja terlebih dahulu untuk dapat mengirimkan lamaran ke pekerjaan ini.</p>
                        <a href="{{ route('login') }}" class="bg-blue-600 text-white font-bold py-2.5 px-8 rounded-lg hover:bg-blue-700 shadow-md transition-all">Login Sekarang</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection