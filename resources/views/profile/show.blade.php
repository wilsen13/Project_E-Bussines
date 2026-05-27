@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-blue-600 font-medium mb-6 inline-flex items-center gap-2 transition-colors">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <!-- Profile Header -->
    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-8">
        <div class="relative h-36 bg-gradient-to-r from-blue-600 to-blue-800">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
        </div>
        <div class="px-8 pb-8 relative">
            <div class="flex flex-col sm:flex-row items-start sm:items-end gap-6 -mt-12">
                <img class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg" src="https://ui-avatars.com/api/?name={{ urlencode($user->fullName) }}&background=2563EB&color=ffffff&bold=true&size=128" alt="{{ $user->fullName }}">
                <div class="flex-grow pt-2">
                    <h1 class="text-2xl font-extrabold text-gray-900">{{ $user->fullName }}</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-1.5">
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium {{ $user->role === 'EMPLOYER' ? 'text-blue-600 bg-blue-50 border border-blue-200' : 'text-emerald-600 bg-emerald-50 border border-emerald-200' }} px-3 py-1 rounded-full">
                            <i class="fas {{ $user->role === 'EMPLOYER' ? 'fa-building' : 'fa-user-tie' }}"></i>
                            {{ $user->role === 'EMPLOYER' ? 'Pemberi Kerja' : 'Pencari Kerja' }}
                        </span>
                        @if($user->rating)
                            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full">
                                <i class="fas fa-star"></i> {{ number_format($user->rating, 1) }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                            <i class="fas fa-check-circle text-green-500"></i> {{ $completedContracts }} pekerjaan selesai
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Left Column: Info -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-id-card text-blue-600"></i> Informasi</h3>
                <div class="space-y-3 text-sm">
                    @if($user->email)
                        <div class="flex items-center gap-3 text-gray-600">
                            <i class="fas fa-envelope text-gray-400 w-4"></i> {{ $user->email }}
                        </div>
                    @endif
                    @if($user->phone)
                        <div class="flex items-center gap-3 text-gray-600">
                            <i class="fas fa-phone text-gray-400 w-4"></i> {{ $user->phone }}
                        </div>
                    @endif
                    @if($user->role === 'JOB_SEEKER' && $user->jobSeeker && $user->jobSeeker->education)
                        <div class="flex items-start gap-3 text-gray-600">
                            <i class="fas fa-graduation-cap text-gray-400 w-4 mt-0.5"></i> 
                            <span>{{ $user->jobSeeker->education }}</span>
                        </div>
                    @endif
                    @if($user->createdAt)
                        <div class="flex items-center gap-3 text-gray-600">
                            <i class="fas fa-calendar-alt text-gray-400 w-4"></i> Bergabung {{ date('M Y', strtotime($user->createdAt)) }}
                        </div>
                    @endif
                </div>
            </div>

            @if($user->bio)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2"><i class="fas fa-quote-left text-blue-600"></i> Tentang</h3>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $user->bio }}</p>
                </div>
            @endif
        </div>

        <!-- Right Column: Reviews -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-star text-amber-500"></i> Ulasan ({{ $reviews->count() }})
                </h3>
                @if($reviews->isEmpty())
                    <div class="text-center py-8">
                        <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                            <i class="far fa-comment-dots text-xl"></i>
                        </div>
                        <p class="text-sm text-gray-500">Belum ada ulasan untuk pengguna ini.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($reviews as $review)
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <img class="w-9 h-9 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($review->reviewer->fullName ?? 'U') }}&background=EBF4FF&color=2563EB&bold=true&size=64" alt="">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $review->reviewer->fullName ?? 'Anonim' }}</p>
                                            <p class="text-xs text-gray-500">{{ $review->contract && $review->contract->job ? $review->contract->job->title : '' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 text-amber-500">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="text-sm text-gray-600 leading-relaxed mt-2">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
