<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Microtask Joblink</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- AlpineJS for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen text-gray-800">

    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo & Brand -->
                <a href="{{ route('home') }}" class="flex items-center shrink-0 cursor-pointer">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex justify-center items-center mr-3 shadow-md">
                        <i class="fas fa-briefcase text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-gray-900 hidden sm:block">Microtask <span class="text-blue-600">Joblink</span></span>
                </a>

                <!-- Search Bar (Center) -->
                <div class="flex-1 max-w-2xl px-8 hidden md:block">
                    <form action="{{ route('home') }}" method="GET" class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" class="block w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-full leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all duration-300 sm:text-sm" placeholder="Cari pekerjaan, proyek, atau keahlian...">
                    </form>
                </div>

                <!-- Right Nav Elements -->
                <div class="flex items-center gap-6">
                    <!-- Nav Links -->
                    <div class="hidden lg:flex items-center gap-6 text-gray-500 font-medium text-sm">
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-blue-600 font-semibold' : 'hover:text-blue-600' }} flex items-center gap-2 transition-colors">
                            <i class="fas fa-home text-lg"></i>
                            <span>Beranda</span>
                        </a>
                        @auth
                        <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.index') ? 'text-blue-600 font-semibold' : 'hover:text-blue-600' }} transition-colors flex items-center gap-2">
                            <i class="fas fa-history text-lg"></i>
                            <span>Aktivitas</span>
                        </a>
                        <a href="{{ route('wallet.index') }}" class="{{ request()->routeIs('wallet.index') ? 'text-blue-600 font-semibold' : 'hover:text-blue-600' }} transition-colors flex items-center gap-2">
                            <i class="fas fa-wallet text-lg"></i>
                            <span>Dompet</span>
                        </a>
                        @endauth
                    </div>
                    
                    <div class="w-px h-8 bg-gray-200 hidden lg:block"></div>

                    <!-- User Profile & Notifications -->
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-400 hover:text-blue-600 hover:bg-gray-100 rounded-full transition-colors">
                                <i class="far fa-bell text-xl"></i>
                            </a>
                            
                            <div class="flex items-center gap-3 cursor-pointer group relative" x-data="{ open: false }" @click.away="open = false" @click="open = !open">
                                <div class="text-right hidden sm:block">
                                    <p class="text-xs text-gray-500 font-medium">Halo,</p>
                                    <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ Auth::user()->fullName }}</p>
                                </div>
                                <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm group-hover:border-blue-100 transition-colors" src="{{ Auth::user()->avatar_url }}" alt="Profile">
                                
                                <!-- Dropdown -->
                                <div x-show="open" class="absolute right-0 top-14 w-56 bg-white rounded-xl shadow-lg py-2 border border-gray-100 focus:outline-none" style="display: none;">
                                    <div class="px-4 py-2 border-b border-gray-50 mb-1">
                                        <p class="text-sm font-bold text-gray-900">{{ Auth::user()->fullName }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"><i class="fas fa-user-cog w-5 text-center mr-1"></i> Pengaturan Profil</a>
                                    <a href="{{ route('wallet.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"><i class="fas fa-wallet w-5 text-center mr-1"></i> Dompet & Pembayaran</a>
                                    <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-gray-50 pt-1">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"><i class="fas fa-sign-out-alt w-5 text-center mr-1"></i> Keluar</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:text-blue-800 transition-colors">Masuk</a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 shadow-sm shadow-blue-600/30 transition-all">Daftar</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Simple Footer for SaaS feel -->
    <footer class="bg-white border-t border-gray-200 mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
            <p>&copy; 2024 Microtask Joblink. Hak Cipta Dilindungi.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-blue-600 transition-colors">Tentang Kami</a>
                <a href="#" class="hover:text-blue-600 transition-colors">Kebijakan Privasi</a>
                <a href="#" class="hover:text-blue-600 transition-colors">Syarat & Ketentuan</a>
            </div>
        </div>
    </footer>

</body>
</html>
