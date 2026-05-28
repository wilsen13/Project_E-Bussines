@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Eksplorasi Peluang Baru</h2>
            <p class="text-gray-500 mt-1">Temukan pekerjaan micro-tasking yang sesuai dengan kemampuanmu</p>
        </div>
        @auth
            @if(auth()->user()->role === 'EMPLOYER')
                <a href="{{ route('jobs.create') }}" class="bg-blue-600 text-white font-bold py-2.5 px-5 rounded-lg hover:bg-blue-700 shadow-md shadow-blue-600/20 transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Buat Lowongan
                </a>
            @endif
        @endauth
    </div>

    <div class="mb-8 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
        <form action="{{ route('home') }}" method="GET" class="w-full flex items-center gap-4">
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all duration-300 sm:text-sm" placeholder="Cari pekerjaan berdasarkan judul atau kategori...">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md">
                Cari
            </button>
            @if(request('q'))
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-red-500 transition-colors font-medium text-sm">Reset</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- ============ Active Jobs Quick Access ============ --}}
    @auth
    @if(isset($activeContracts) && $activeContracts->count() > 0)
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bolt text-white text-sm"></i>
                </div>
                <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">Pekerjaan Aktif Saat Ini</h3>
                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $activeContracts->count() }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeContracts as $ac)
                <div class="bg-white rounded-2xl border {{ $ac->status === 'WAITING_REVIEW' ? 'border-amber-300 shadow-amber-100' : 'border-blue-200 shadow-blue-100' }} shadow-sm hover:shadow-md transition-all p-5 flex flex-col gap-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="shrink-0 w-10 h-10 rounded-xl {{ $ac->status === 'WAITING_REVIEW' ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600' }} flex items-center justify-center">
                                <i class="fas {{ $ac->status === 'WAITING_REVIEW' ? 'fa-hourglass-half' : 'fa-briefcase' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-gray-900 text-sm leading-tight line-clamp-1">{{ $ac->job->title ?? 'Pekerjaan' }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">
                                    @if(auth()->user()->role === 'JOB_SEEKER')
                                        <i class="fas fa-building opacity-70"></i> {{ $ac->job->employer->displayName ?? 'Anonim' }}
                                    @else
                                        <i class="fas fa-user opacity-70"></i> {{ $ac->jobSeeker->user->fullName ?? 'Anonim' }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($ac->status === 'WAITING_REVIEW')
                            <span class="shrink-0 bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap">Menunggu Review</span>
                        @else
                            <span class="shrink-0 bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap">Aktif</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <span class="text-sm font-bold text-green-600">Rp {{ number_format($ac->payment->amount ?? 0, 0, ',', '.') }}</span>
                        <a href="{{ route('contracts.show', $ac->contractID) }}" class="text-blue-600 hover:text-blue-700 text-xs font-bold inline-flex items-center gap-1 transition-colors">
                            Lihat Detail <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif
    @endauth

    @if($jobs->isEmpty())
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center text-gray-500">
            Belum ada lowongan pekerjaan saat ini.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($jobs as $job)
            <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group cursor-pointer">
                @if($job->image_url)
                <div class="h-40 w-full overflow-hidden bg-gray-100">
                    @if(str_starts_with($job->image_url, 'http'))
                        <img src="{{ $job->image_url }}" alt="Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <img src="{{ asset('storage/' . $job->image_url) }}" alt="Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @endif
                </div>
                @endif
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="font-bold text-lg text-gray-900 leading-tight group-hover:text-blue-600 transition-colors line-clamp-2 mb-2">{{ $job->title }}</h3>
                    <div class="flex items-center gap-2 mb-4">
                        @if($job->employer && $job->employer->user)
                            <img class="w-6 h-6 rounded-full object-cover" src="{{ $job->employer->user->avatar_url }}" alt="">
                        @else
                            <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">{{ substr($job->employer->displayName ?? 'User', 0, 1) }}</div>
                        @endif
                        <p class="text-sm text-gray-500 font-medium line-clamp-1">Oleh <span class="text-gray-700">{{ $job->employer->displayName ?? 'Anonim' }}</span></p>
                    </div>
                    <div class="mt-auto pt-4 border-t border-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                @if($job->is_remote)
                                    <p class="text-xs text-blue-600 font-bold flex items-center gap-1.5 mb-1 bg-blue-50 px-2 py-0.5 rounded-md inline-flex"><i class="fas fa-laptop"></i> Kerja Jarak Jauh (Remote)</p>
                                @else
                                    <p class="text-xs text-gray-400 flex items-center gap-1.5 mb-1"><i class="fas fa-map-marker-alt text-red-400"></i> {{ $job->location ? $job->location->city : 'Lokasi Fisik' }}</p>
                                @endif
                                <p class="text-lg font-black text-green-600">Rp {{ number_format($job->payAmount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @auth
                                @if(auth()->user()->role === 'EMPLOYER' && $job->employerID === auth()->user()->userID)
                                    <a href="{{ route('jobs.edit', $job->jobID) }}" class="bg-amber-100 text-amber-600 hover:bg-amber-600 hover:text-white rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('jobs.destroy', $job->jobID) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus lowongan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            @endauth
                            <a href="{{ route('jobs.show', $job->jobID) }}" class="bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors shadow-sm inline-flex items-center ml-auto">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection