@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gray-100/70">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-blue-600 font-medium mb-6 inline-flex items-center gap-2 transition-colors group">
            <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Kembali
        </a>

        {{-- ====== PROFILE HEADER CARD ====== --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/60 border border-gray-100 overflow-hidden mb-8">
            {{-- Banner --}}
            <div class="relative h-40 bg-gradient-to-br from-indigo-600 via-blue-600 to-cyan-500 overflow-hidden">
                <div class="absolute inset-0">
                    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-48 h-48 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-indigo-400/10 blur-3xl"></div>
                </div>
                {{-- Subtle pattern overlay --}}
                <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            {{-- Profile Info Section --}}
            <div class="px-8 pb-8 relative">
                {{-- Avatar (overlapping banner) --}}
                <div class="flex justify-center sm:justify-start -mt-16 mb-5 relative z-10">
                    <img class="w-32 h-32 rounded-2xl object-cover border-4 border-white shadow-xl ring-2 ring-gray-100" 
                         src="{{ $user->avatar_url }}" 
                         alt="{{ $user->fullName }}">
                </div>

                {{-- Name & Badges (safely in white zone) --}}
                <div class="text-center sm:text-left">
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $user->fullName }}</h1>
                    @if($user->username)
                        <p class="text-sm text-gray-400 font-medium mt-0.5">{{ '@' . $user->username }}</p>
                    @endif

                    <div class="flex flex-wrap items-center gap-2.5 mt-3 justify-center sm:justify-start">
                        {{-- Role Badge --}}
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold {{ $user->role === 'EMPLOYER' ? 'text-indigo-700 bg-indigo-50 border border-indigo-200' : 'text-emerald-700 bg-emerald-50 border border-emerald-200' }} px-3.5 py-1.5 rounded-full">
                            <i class="fas {{ $user->role === 'EMPLOYER' ? 'fa-building' : 'fa-user-tie' }} text-xs"></i>
                            {{ $user->role === 'EMPLOYER' ? 'Pemberi Kerja' : 'Pencari Kerja' }}
                        </span>

                        {{-- Rating Badge --}}
                        @if($user->rating)
                            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-amber-700 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 px-3.5 py-1.5 rounded-full">
                                <i class="fas fa-star text-amber-400 text-xs"></i> {{ number_format($user->rating, 1) }}
                            </span>
                        @endif

                        {{-- Completed Jobs Badge --}}
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 px-3.5 py-1.5 rounded-full">
                            <i class="fas fa-check-circle text-green-500 text-xs"></i> {{ $completedContracts }} pekerjaan selesai
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- ====== LEFT COLUMN: INFO ====== --}}
            <div class="md:col-span-1 space-y-6">
                {{-- Contact Information Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-id-card text-blue-600 text-xs"></i>
                            </div>
                            Informasi
                        </h3>
                    </div>
                    <div class="p-6 space-y-3.5">
                        @if($user->email)
                            <div class="flex items-center gap-3 text-sm text-gray-600 group">
                                <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-blue-50 transition-colors">
                                    <i class="fas fa-envelope text-gray-400 group-hover:text-blue-500 transition-colors text-xs"></i>
                                </div>
                                <span class="truncate">{{ $user->email }}</span>
                            </div>
                        @endif
                        @if($user->phone)
                            <div class="flex items-center gap-3 text-sm text-gray-600 group">
                                <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-green-50 transition-colors">
                                    <i class="fas fa-phone text-gray-400 group-hover:text-green-500 transition-colors text-xs"></i>
                                </div>
                                <span>{{ $user->phone }}</span>
                            </div>
                        @endif
                        @if($user->role === 'JOB_SEEKER' && $user->jobSeeker && $user->jobSeeker->education)
                            <div class="flex items-start gap-3 text-sm text-gray-600 group">
                                <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-purple-50 transition-colors">
                                    <i class="fas fa-graduation-cap text-gray-400 group-hover:text-purple-500 transition-colors text-xs"></i>
                                </div>
                                <span>{{ $user->jobSeeker->education }}</span>
                            </div>
                        @endif
                        @if($user->createdAt)
                            <div class="flex items-center gap-3 text-sm text-gray-600 group">
                                <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-amber-50 transition-colors">
                                    <i class="fas fa-calendar-alt text-gray-400 group-hover:text-amber-500 transition-colors text-xs"></i>
                                </div>
                                <span>Bergabung {{ date('M Y', strtotime($user->createdAt)) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Bio Card --}}
                @if($user->bio)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                                <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-quote-left text-indigo-600 text-xs"></i>
                                </div>
                                Tentang
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $user->bio }}</p>
                        </div>
                    </div>
                @endif

                {{-- Stats Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl">
                            <p class="text-2xl font-black text-blue-600">{{ $completedContracts }}</p>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">Selesai</p>
                        </div>
                        <div class="text-center p-3 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl">
                            <p class="text-2xl font-black text-amber-600">{{ $user->rating ? number_format($user->rating, 1) : '-' }}</p>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">Rating</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ====== RIGHT COLUMN: REVIEWS ====== --}}
            <div class="md:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-5 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-amber-500 text-sm"></i>
                            </div>
                            Ulasan
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded-full ml-1">{{ $reviews->count() }}</span>
                        </h3>
                    </div>

                    <div class="p-6">
                        @if($reviews->isEmpty())
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="far fa-comment-dots text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-sm text-gray-400 font-medium">Belum ada ulasan untuk pengguna ini.</p>
                                <p class="text-xs text-gray-300 mt-1">Ulasan akan muncul setelah pekerjaan selesai.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($reviews as $review)
                                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-5 border border-gray-100 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                <img class="w-10 h-10 rounded-xl object-cover border border-gray-100" 
                                                     src="{{ $review->reviewer->avatar_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($review->reviewer->fullName ?? 'U') . '&background=EBF4FF&color=2563EB&bold=true&size=64') }}" 
                                                     alt="">
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900">{{ $review->reviewer->fullName ?? 'Anonim' }}</p>
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ $review->contract && $review->contract->job ? $review->contract->job->title : '' }}</p>
                                                </div>
                                            </div>
                                            {{-- Star Rating --}}
                                            <div class="flex items-center gap-0.5 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-100">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    @else
                                                        <svg class="w-3.5 h-3.5 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    @endif
                                                @endfor
                                                <span class="text-xs font-bold text-amber-600 ml-1">{{ $review->rating }}.0</span>
                                            </div>
                                        </div>
                                        @if($review->comment)
                                            <p class="text-sm text-gray-600 leading-relaxed pl-13">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
