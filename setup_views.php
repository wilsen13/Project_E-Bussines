<?php

$views = [
    'auth/register.blade.php' => <<<EOT
@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-8 rounded-xl shadow-md border border-gray-100">
    <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Daftar Akun Baru</h2>
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
            <input type="text" name="fullName" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-600 focus:border-blue-600" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
            <input type="text" name="username" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-600 focus:border-blue-600" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input type="email" name="email" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-600 focus:border-blue-600" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Telepon (Min 10 digit)</label>
            <input type="text" name="phone" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-600 focus:border-blue-600" required minlength="10">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Password (Min 8 karakter)</label>
            <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-600 focus:border-blue-600" required minlength="8">
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Peran</label>
            <select name="role" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-600 focus:border-blue-600" required>
                <option value="JOB_SEEKER">Pencari Kerja (Job Seeker)</option>
                <option value="EMPLOYER">Pemberi Kerja (Employer)</option>
            </select>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition">Daftar</button>
    </form>
    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-blue-600 text-sm hover:underline">Sudah punya akun? Login</a>
    </div>
</div>
@endsection
EOT,
    'auth/login.blade.php' => <<<EOT
@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-8 rounded-xl shadow-md border border-gray-100">
    <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Login</h2>
    @if(\$errors->any())
        <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
            {{ \$errors->first() }}
        </div>
    @endif
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Username atau Email</label>
            <input type="text" name="username" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-600 focus:border-blue-600" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-600 focus:border-blue-600" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition">Login</button>
    </form>
    <div class="mt-4 text-center">
        <a href="{{ route('register') }}" class="text-blue-600 text-sm hover:underline">Belum punya akun? Daftar</a>
    </div>
</div>
@endsection
EOT,
    'home.blade.php' => <<<EOT
@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
    <div class="mb-8 flex justify-between items-center">
        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Lowongan Pekerjaan Terbaru</h2>
        @if(auth()->user()->role === 'EMPLOYER')
            <button class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition">Buat Lowongan</button>
        @endif
    </div>

    @if(\$jobs->isEmpty())
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center text-gray-500">
            Belum ada lowongan pekerjaan saat ini.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach(\$jobs as \$job)
            <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group cursor-pointer">
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="font-bold text-lg text-gray-900 leading-tight group-hover:text-blue-600 transition-colors line-clamp-2 mb-2">{{ \$job->title }}</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">{{ substr(\$job->employer->displayName, 0, 1) }}</div>
                        <p class="text-sm text-gray-500 font-medium line-clamp-1">Oleh <span class="text-gray-700">{{ \$job->employer->displayName }}</span></p>
                    </div>
                    <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 flex items-center gap-1.5 mb-1"><i class="fas fa-map-marker-alt"></i> {{ \$job->location ? \$job->location->city : 'Remote' }}</p>
                            <p class="text-lg font-black text-blue-600">Rp {{ number_format(\$job->payAmount, 0, ',', '.') }}</p>
                        </div>
                        <a href="{{ route('jobs.show', \$job->jobID) }}" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors shadow-sm">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
EOT,
    'jobs/show.blade.php' => <<<EOT
@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="{{ route('home') }}" class="text-blue-600 hover:underline mb-4 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
    
    @if(session('error'))
        <div class="bg-red-100 text-red-600 p-4 rounded-xl mb-6 font-medium">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ \$job->title }}</h1>
            <div class="flex items-center gap-4 text-sm text-gray-500 mb-6 pb-6 border-b border-gray-100">
                <span class="flex items-center gap-2"><i class="fas fa-building text-blue-500"></i> {{ \$job->employer->displayName }}</span>
                <span class="flex items-center gap-2"><i class="fas fa-map-marker-alt text-red-500"></i> {{ \$job->location ? \$job->location->city : 'N/A' }}</span>
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-bold text-xs">Rp {{ number_format(\$job->payAmount, 0, ',', '.') }}</span>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-3">Deskripsi Pekerjaan</h3>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ \$job->description }}</p>
            </div>

            @if(\$job->location)
            <div class="mb-8">
                <a href="{{ route('jobs.address', \$job->jobID) }}" class="text-blue-600 font-semibold hover:underline flex items-center gap-2">
                    <i class="fas fa-map"></i> Lihat Detail Alamat
                </a>
            </div>
            @endif

            <div class="mt-8 pt-6 border-t border-gray-100">
                @if(auth()->user()->role === 'JOB_SEEKER')
                    <form action="{{ route('applications.store', \$job->jobID) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-blue-700 shadow-md transition-all">Lamar Pekerjaan Ini</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
EOT,
    'jobs/address.blade.php' => <<<EOT
@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <a href="{{ route('jobs.show', \$job->jobID) }}" class="text-blue-600 hover:underline mb-4 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Detail Pekerjaan</a>
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Detail Alamat</h2>
            
            <div class="w-full h-64 bg-gray-200 rounded-xl mb-6 flex items-center justify-center">
                <i class="fas fa-map-marker-alt text-4xl text-gray-400"></i>
                <span class="ml-2 text-gray-500 font-medium">Map Placeholder</span>
            </div>

            <div class="bg-blue-50 p-6 rounded-xl">
                <h4 class="font-bold text-gray-900 mb-2">Alamat Lengkap</h4>
                <p class="text-gray-700 mb-1">{{ \$job->location->addressLine }}</p>
                <p class="text-gray-700">{{ \$job->location->city }}, {{ \$job->location->province }} {{ \$job->location->postalCode }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
EOT,
    'notifications/index.blade.php' => <<<EOT
@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Notifikasi & Status</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6 font-medium">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Notifikasi Umum -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Umum</h3>
            @if(\$notifications->isEmpty())
                <p class="text-gray-500">Belum ada notifikasi.</p>
            @else
                <div class="space-y-4">
                    @foreach(\$notifications as \$notif)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <h4 class="font-bold text-gray-900">{{ \$notif->title }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ \$notif->message }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ date('d M Y, H:i', strtotime(\$notif->sentAt)) }}</p>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Status Lamaran -->
        @if(auth()->user()->role === 'JOB_SEEKER')
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Status Lamaran</h3>
            @if(count(\$applications) == 0)
                <p class="text-gray-500">Anda belum melamar pekerjaan apapun.</p>
            @else
                <div class="space-y-4">
                    @foreach(\$applications as \$app)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                        <div>
                            <h4 class="font-bold text-gray-900">{{ \$app->job->title }}</h4>
                            <p class="text-sm text-gray-500">{{ \$app->job->employer->displayName }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ date('d M Y', strtotime(\$app->createdAt)) }}</p>
                        </div>
                        <div>
                            @if(\$app->status === 'APPLIED')
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold">Lamaran Terkirim</span>
                            @elseif(\$app->status === 'REJECTED')
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Lamaran Ditolak</span>
                            @elseif(\$app->status === 'SHORTLISTED')
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">Menunggu Respon</span>
                            @elseif(\$app->status === 'HIRED')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Lamaran Diterima</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
EOT,
];

foreach ($views as $path => $content) {
   $fullPath = base_path('resources/views/' . $path);
    $dir = dirname($fullPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($fullPath, $content);
}
echo "Views generated.\n";
