@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pengaturan Profil</h1>
        <p class="text-gray-500 mt-1">Kelola informasi pribadi dan keamanan akun Anda</p>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success_profile'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl mb-6 flex items-center gap-3 animate-fade-in">
            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                <i class="fas fa-check text-emerald-600"></i>
            </div>
            <p class="text-sm text-emerald-700 font-medium">{{ session('success_profile') }}</p>
        </div>
    @endif

    {{-- ====== AVATAR SECTION ====== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="h-24 bg-gradient-to-r from-indigo-500 via-blue-600 to-cyan-500 relative">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMjAgMEwyNSAxMEwzNSAxMEwyNyAxN0wzMCAyN0wyMCAyMkwxMCAyN0wxMyAxN0w1IDEwTDE1IDEwWiIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjA1KSIvPjwvc3ZnPg==')] opacity-30"></div>
        </div>
        <div class="px-8 pb-8 relative">
            <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6 -mt-12">
                {{-- Avatar Display --}}
                <div class="relative group">
                    <img id="avatarPreview" 
                         class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg ring-2 ring-gray-100 transition-transform group-hover:scale-105" 
                         src="{{ $user->avatar_url }}" 
                         alt="{{ $user->fullName }}">
                    <div class="absolute inset-0 rounded-2xl bg-black/0 group-hover:bg-black/20 transition-all flex items-center justify-center">
                        <i class="fas fa-camera text-white opacity-0 group-hover:opacity-100 transition-opacity text-lg"></i>
                    </div>
                </div>

                {{-- Avatar Actions --}}
                <div class="flex-grow text-center sm:text-left pb-1">
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->fullName }}</h2>
                    <p class="text-sm text-gray-500 mb-3">{{ $user->email }}</p>
                    <div class="flex flex-wrap items-center gap-2 justify-center sm:justify-start">
                        {{-- Upload Avatar Button --}}
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="avatarForm" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="fullName" value="{{ $user->fullName }}">
                            <input type="hidden" name="phone" value="{{ $user->phone }}">
                            <input type="hidden" name="bio" value="{{ $user->bio }}">
                            <label for="avatarInput" class="inline-flex items-center gap-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-sm px-4 py-2 rounded-xl cursor-pointer transition-colors border border-indigo-200">
                                <i class="fas fa-cloud-upload-alt"></i> Unggah Foto
                            </label>
                            <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this); document.getElementById('avatarForm').submit();">
                        </form>

                        {{-- Delete Avatar Button --}}
                        @if($user->avatar)
                            <form action="{{ route('profile.avatar.delete') }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus foto profil?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-sm px-4 py-2 rounded-xl transition-colors border border-red-200">
                                    <i class="fas fa-trash-alt"></i> Hapus Foto
                                </button>
                            </form>
                        @endif
                    </div>
                    @error('avatar') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- ====== EDIT PROFILE FORM ====== --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-white px-8 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-edit text-blue-600 text-sm"></i>
                        </div>
                        Informasi Pribadi
                    </h3>
                </div>
                <div class="p-8">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-5">
                            <div>
                                <label for="fullName" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" id="fullName" name="fullName" value="{{ old('fullName', $user->fullName) }}" required class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-gray-50 focus:bg-white">
                                </div>
                                @error('fullName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor Telepon</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required minlength="10" class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-gray-50 focus:bg-white">
                                </div>
                                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="bio" class="block text-sm font-semibold text-gray-700 mb-1.5">Bio</label>
                                <textarea id="bio" name="bio" rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all resize-none bg-gray-50 focus:bg-white" placeholder="Ceritakan sedikit tentang diri Anda atau keahlian Anda...">{{ old('bio', $user->bio) }}</textarea>
                                <p class="mt-1.5 text-xs text-gray-400 flex items-center gap-1"><i class="fas fa-info-circle"></i> Maks. 300 karakter. Tampil di profil publik Anda.</p>
                                @error('bio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-blue-600/25 hover:shadow-blue-600/40 flex items-center gap-2">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ====== CHANGE PASSWORD FORM ====== --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-28">
                <div class="bg-gradient-to-r from-gray-50 to-white px-8 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-lock text-amber-600 text-sm"></i>
                        </div>
                        Ubah Password
                    </h3>
                </div>
                <div class="p-8">
                    @if(session('success_password'))
                        <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-xl mb-5 flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            <p class="text-sm text-emerald-700 font-medium">{{ session('success_password') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password Saat Ini</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-key text-gray-400"></i>
                                    </div>
                                    <input type="password" id="current_password" name="current_password" required class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-gray-50 focus:bg-white">
                                </div>
                                @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password Baru</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input type="password" id="new_password" name="new_password" required minlength="8" class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-gray-50 focus:bg-white">
                                </div>
                                @error('new_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-shield-alt text-gray-400"></i>
                                    </div>
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required minlength="8" class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-gray-50 focus:bg-white">
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-bold py-2.5 px-4 rounded-xl transition-all shadow-lg shadow-gray-900/20 flex items-center justify-center gap-2">
                                    <i class="fas fa-sync-alt"></i> Ubah Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.4s ease-out; }
</style>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
