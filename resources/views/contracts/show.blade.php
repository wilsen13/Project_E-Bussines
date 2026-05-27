@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <a href="{{ route('home') }}" class="text-gray-500 hover:text-blue-600 font-medium mb-6 inline-flex items-center gap-2 transition-colors">
        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
    </a>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-5 rounded-lg mb-6 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-green-800 mb-0.5">Berhasil!</h4>
                    <p class="text-sm text-green-700 leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Error Alert --}}
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-6">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-6 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-700 font-medium">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @php
        $job = $contract->job;
        $isSeeker = auth()->user()->userID === $contract->jobSeekerID;
        $isEmployer = auth()->user()->userID === $contract->employerID;
    @endphp

    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        {{-- ================================================================ --}}
        {{-- HERO HEADER — Identical to jobs.show                             --}}
        {{-- ================================================================ --}}
        <div class="relative w-full h-64 sm:h-80 bg-gray-900 overflow-hidden">
            @if($job->image_url)
                <img src="{{ $job->image_url }}" alt="Thumbnail" class="w-full h-full object-cover opacity-60">
            @else
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-800"></div>
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
            @endif
            <div class="absolute inset-0 flex flex-col md:flex-row md:items-end justify-between gap-6 p-8 sm:p-10 text-white bg-gradient-to-t from-gray-900 to-transparent">
                <div class="w-full max-w-2xl">
                    {{-- Contract Status Badge --}}
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-500/80 backdrop-blur-md rounded-full text-blue-50 text-xs font-semibold border border-blue-400/50 shadow-sm">
                            <i class="fas fa-briefcase"></i> {{ $job->category ? $job->category->name : 'Umum' }}
                        </div>
                        @if($contract->status === 'ACTIVE')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/80 backdrop-blur-md rounded-full text-white text-xs font-bold border border-emerald-400/50 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> Kontrak Aktif
                            </div>
                        @elseif($contract->status === 'WAITING_REVIEW')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/80 backdrop-blur-md rounded-full text-white text-xs font-bold border border-amber-400/50 shadow-sm">
                                <i class="fas fa-hourglass-half"></i> Menunggu Review
                            </div>
                        @elseif($contract->status === 'COMPLETED')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-600/80 backdrop-blur-md rounded-full text-white text-xs font-bold border border-green-400/50 shadow-sm">
                                <i class="fas fa-check-double"></i> Selesai
                            </div>
                        @endif
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
                    <p class="text-xs text-blue-100 font-bold uppercase tracking-wider mb-1">Nilai Kontrak</p>
                    <p class="text-2xl font-black drop-shadow-md">Rp {{ number_format($contract->payment->amount ?? $job->payAmount, 0, ',', '.') }}</p>
                    <p class="text-xs text-blue-200 mt-1 font-medium">
                        @if($contract->payment && $contract->payment->status === 'HELD')
                            <i class="fas fa-lock"></i> Escrow Ditahan
                        @elseif($contract->payment && $contract->payment->status === 'RELEASED')
                            <i class="fas fa-check-circle"></i> Dana Dicairkan
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- CONTENT BODY                                                     --}}
        {{-- ================================================================ --}}
        <div class="p-8 sm:p-10">
            {{-- Contract Parties Info --}}
            <div class="mb-8 bg-gray-50 rounded-2xl p-5 border border-gray-100">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4"><i class="fas fa-handshake text-blue-600 mr-2"></i>Pihak Kontrak</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-4 bg-white rounded-xl p-4 border border-gray-100">
                        <img class="w-12 h-12 rounded-xl object-cover border-2 border-blue-100 shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode($job->employer->displayName ?? 'E') }}&background=EBF4FF&color=2563EB&bold=true" alt="Employer">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pemberi Kerja</p>
                            <p class="font-bold text-gray-900">{{ $job->employer->displayName ?? 'Anonim' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white rounded-xl p-4 border border-gray-100">
                        <img class="w-12 h-12 rounded-xl object-cover border-2 border-emerald-100 shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode($contract->jobSeeker->user->fullName ?? 'S') }}&background=D1FAE5&color=059669&bold=true" alt="Seeker">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pekerja</p>
                            <p class="font-bold text-gray-900">{{ $contract->jobSeeker->user->fullName ?? 'Anonim' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Job Description --}}
            <div class="mb-10 prose prose-blue max-w-none">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2 border-b border-gray-100 pb-3">
                    <i class="fas fa-align-left text-blue-600"></i> Deskripsi Pekerjaan
                </h3>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $job->description }}</p>
            </div>

            {{-- Expandable Location --}}
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
                    <div class="flex gap-3 text-sm">
                        <i class="fas fa-location-dot mt-1 text-red-500"></i>
                        <div>
                            <p class="font-bold text-gray-900">Alamat Lengkap</p>
                            <p class="text-gray-600">{{ $job->location->addressLine }}</p>
                            <p class="text-gray-600">{{ $job->location->city }}, {{ $job->location->province }} {{ $job->location->postalCode }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ================================================================ --}}
            {{-- BOTTOM SECTION — Dynamic based on role                           --}}
            {{-- ================================================================ --}}
            <div class="mt-10 pt-8 border-t border-gray-200">

                {{-- ============================================================ --}}
                {{-- JOB SEEKER VIEW                                              --}}
                {{-- ============================================================ --}}
                @if($isSeeker)
                    @if($contract->status === 'ACTIVE')
                        {{-- Revision Warning --}}
                        @if($contract->revision_notes)
                            <div class="bg-amber-50 border-l-4 border-amber-400 rounded-r-xl p-5 mb-6">
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0 w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-amber-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-amber-800 mb-1">Revisi Diminta oleh Employer</h4>
                                        <p class="text-sm text-amber-700 leading-relaxed">{{ $contract->revision_notes }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Upload Proof Form --}}
                        <div class="bg-blue-50 rounded-2xl p-6 md:p-8 border border-blue-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                                <i class="fas fa-cloud-upload-alt text-blue-600"></i> Upload Bukti Pekerjaan
                            </h3>
                            <p class="text-sm text-gray-600 mb-6">Unggah file hasil pekerjaan dan sertakan deskripsi atau link pendukung. Format yang diterima: JPG, PNG, PDF, DOC, ZIP (maks. 10MB).</p>
                            
                            <form action="{{ route('contracts.submitProof', $contract->contractID) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-5">
                                    {{-- File Input --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">File Bukti Pekerjaan <span class="text-gray-400 font-normal">(opsional)</span></label>
                                        <div class="relative" x-data="{ fileName: '' }">
                                            <input type="file" name="proof_file" id="proof_file" class="hidden" @change="fileName = $event.target.files[0]?.name || ''" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.zip,.rar">
                                            <label for="proof_file" class="cursor-pointer flex items-center gap-4 bg-white border-2 border-dashed border-gray-300 hover:border-blue-400 rounded-xl p-5 transition-colors group">
                                                <div class="shrink-0 w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-xl flex items-center justify-center transition-colors">
                                                    <i class="fas fa-file-upload text-blue-600 text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-700 text-sm" x-text="fileName || 'Klik untuk memilih file'"></p>
                                                    <p class="text-xs text-gray-400 mt-0.5">JPG, PNG, PDF, DOC, ZIP — Maks. 10MB</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Text Description --}}
                                    <div>
                                        <label for="proof_of_work" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi / Link Hasil Pekerjaan <span class="text-red-500">*</span></label>
                                        <textarea id="proof_of_work" name="proof_of_work" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors resize-none placeholder-gray-400" placeholder="Jelaskan apa yang telah Anda kerjakan, atau sertakan link Google Drive / GitHub / dll..." required>{{ old('proof_of_work') }}</textarea>
                                    </div>

                                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3.5 px-6 rounded-xl hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/30 transition-all duration-300 flex items-center justify-center gap-2">
                                        <i class="fas fa-paper-plane"></i> Kirim Bukti Pekerjaan
                                    </button>
                                </div>
                            </form>
                        </div>

                    @elseif($contract->status === 'WAITING_REVIEW')
                        <div class="bg-amber-50 rounded-2xl p-6 md:p-8 border border-amber-200">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-hourglass-half text-amber-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-amber-900">Menunggu Persetujuan Employer</h3>
                                    <p class="text-sm text-amber-700">Bukti pekerjaan Anda telah dikirim. Silakan tunggu konfirmasi dari pemberi kerja.</p>
                                </div>
                            </div>
                            @if($contract->proof_of_work)
                                <div class="bg-white border border-amber-100 rounded-xl p-4 mt-4">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Bukti yang dikirim</p>
                                    <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $contract->proof_of_work }}</p>
                                </div>
                            @endif
                            @if($contract->proof_file_path)
                                <div class="mt-3 flex items-center gap-3 bg-white border border-amber-100 rounded-xl p-4">
                                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                                        <i class="fas fa-file-alt text-blue-600"></i>
                                    </div>
                                    <div class="flex-grow min-w-0">
                                        <p class="text-sm font-semibold text-gray-700 truncate">{{ basename($contract->proof_file_path) }}</p>
                                        <p class="text-xs text-gray-400">File telah diunggah</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                    @elseif($contract->status === 'COMPLETED')
                        <div class="bg-green-50 rounded-2xl p-6 md:p-8 border border-green-200 text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-green-900 mb-2">Pekerjaan Selesai!</h3>
                            <p class="text-sm text-green-700 mb-6">Pekerjaan telah disetujui dan dana telah dicairkan ke dompet Anda.</p>
                            <a href="{{ route('wallet.index') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition-colors">
                                <i class="fas fa-wallet"></i> Lihat Dompet
                            </a>
                        </div>
                    @endif

                {{-- ============================================================ --}}
                {{-- EMPLOYER VIEW                                                --}}
                {{-- ============================================================ --}}
                @elseif($isEmployer)
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-clipboard-check text-blue-600"></i> Status Pengerjaan
                            </h3>
                        </div>
                        <div class="p-6">
                            @if($contract->status === 'ACTIVE' && !$contract->proof_of_work && !$contract->proof_file_path)
                                {{-- No proof yet --}}
                                <div class="text-center py-10">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-clock text-gray-300 text-3xl"></i>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-700 mb-2">Menunggu Hasil Pekerjaan</h4>
                                    <p class="text-sm text-gray-500 max-w-md mx-auto">Pekerja belum mengunggah hasil pekerjaannya. Anda akan mendapat notifikasi saat bukti pekerjaan telah dikirim.</p>
                                </div>

                            @elseif($contract->status === 'WAITING_REVIEW')
                                {{-- Proof submitted — show it --}}
                                <div class="space-y-5">
                                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                                        <div class="flex items-start gap-3 mb-3">
                                            <div class="shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-file-alt text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-blue-800 mb-0.5">Bukti Pekerjaan dari Freelancer</p>
                                                <p class="text-xs text-blue-600">Dikirim oleh {{ $contract->jobSeeker->user->fullName ?? 'Anonim' }}</p>
                                            </div>
                                        </div>
                                        @if($contract->proof_of_work)
                                            <div class="bg-white border border-blue-100 rounded-lg p-4">
                                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deskripsi / Link</p>
                                                <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $contract->proof_of_work }}</p>
                                            </div>
                                        @endif
                                        @if($contract->proof_file_path)
                                            <div class="mt-3 flex items-center justify-between bg-white border border-blue-100 rounded-lg p-4">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                                                        <i class="fas fa-file-download text-blue-600"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-semibold text-gray-700 truncate">{{ basename($contract->proof_file_path) }}</p>
                                                        <p class="text-xs text-gray-400">File bukti pekerjaan</p>
                                                    </div>
                                                </div>
                                                <a href="{{ asset('storage/' . $contract->proof_file_path) }}" target="_blank" download class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors inline-flex items-center gap-2 shrink-0">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex flex-col gap-3" x-data="{ showRevision: false }">
                                        {{-- Approve --}}
                                        <form action="{{ route('contracts.confirmFromDetail', $contract->contractID) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="APPROVE">
                                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-6 rounded-xl shadow-sm hover:shadow-lg hover:shadow-green-600/20 transition-all flex items-center justify-center gap-2" onclick="return confirm('Setujui pekerjaan ini? Dana escrow akan dicairkan ke freelancer.')">
                                                <i class="fas fa-check-double"></i> Setujui Pekerjaan & Cairkan Dana
                                            </button>
                                        </form>

                                        {{-- Revision Toggle --}}
                                        <button @click="showRevision = !showRevision" class="w-full bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-300 font-bold py-3 px-6 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                                            <i class="fas fa-redo"></i>
                                            <span x-text="showRevision ? 'Tutup Form Revisi' : 'Minta Revisi'"></span>
                                        </button>

                                        {{-- Revision Form --}}
                                        <div x-show="showRevision" x-collapse x-cloak>
                                            <form action="{{ route('contracts.confirmFromDetail', $contract->contractID) }}" method="POST" class="bg-amber-50 border border-amber-200 rounded-xl p-5 space-y-4">
                                                @csrf
                                                <input type="hidden" name="action" value="REVISION">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Revisi untuk Freelancer <span class="text-red-500">*</span></label>
                                                    <textarea name="revision_notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors resize-none placeholder-gray-400" placeholder="Jelaskan apa yang perlu diperbaiki atau dilengkapi..." required></textarea>
                                                </div>
                                                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 px-6 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                                                    <i class="fas fa-paper-plane"></i> Kirim Permintaan Revisi
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @elseif($contract->status === 'COMPLETED')
                                <div class="text-center py-10">
                                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                                    </div>
                                    <h4 class="text-xl font-bold text-green-900 mb-2">Pekerjaan Telah Selesai</h4>
                                    <p class="text-sm text-green-700 mb-6">Dana telah berhasil dicairkan ke dompet freelancer.</p>
                                    @php
                                        $reviewed = \App\Models\Review::where('contractID', $contract->contractID)->where('reviewerUserID', auth()->user()->userID)->exists();
                                    @endphp
                                    @if(!$reviewed)
                                        <a href="{{ route('reviews.create', $contract->contractID) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition-colors">
                                            <i class="fas fa-star"></i> Beri Ulasan
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-2 bg-gray-100 text-gray-600 font-bold py-2.5 px-6 rounded-xl">
                                            <i class="fas fa-check-circle text-green-500"></i> Ulasan telah diberikan
                                        </span>
                                    @endif
                                </div>

                            @else
                                {{-- Active but with revision_notes — Employer waiting for resubmission --}}
                                <div class="text-center py-10">
                                    <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-redo text-amber-400 text-2xl"></i>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-700 mb-2">Menunggu Revisi dari Pekerja</h4>
                                    <p class="text-sm text-gray-500 max-w-md mx-auto mb-4">Anda telah meminta revisi. Menunggu pekerja mengirimkan ulang hasil pekerjaan.</p>
                                    @if($contract->revision_notes)
                                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-left max-w-md mx-auto">
                                            <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Catatan revisi Anda:</p>
                                            <p class="text-sm text-amber-800">{{ $contract->revision_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
