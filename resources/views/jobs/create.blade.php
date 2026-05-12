@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ isRemote: {{ old('is_remote') ? 'true' : 'false' }} }">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Buat Lowongan Baru</h1>
        <p class="text-gray-500 mt-1">Isi detail pekerjaan micro-tasking yang Anda butuhkan</p>
    </div>

    <form action="{{ route('jobs.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div class="space-y-4">
            <h3 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-2">Informasi Pekerjaan</h3>
            
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul Pekerjaan</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors" placeholder="Contoh: Desain Logo Kedai Kopi">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="categoryID" class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                <select id="categoryID" name="categoryID" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->categoryID }}" {{ old('categoryID') == $category->categoryID ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categoryID') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Lengkap</label>
                <textarea id="description" name="description" rows="5" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors" placeholder="Jelaskan secara detail apa yang harus dilakukan pekerja...">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="payAmount" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Bayaran (Rp)</label>
                    <input type="number" id="payAmount" name="payAmount" value="{{ old('payAmount') }}" required min="0" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors" placeholder="Contoh: 150000">
                    @error('payAmount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="image_url" class="block text-sm font-semibold text-gray-700 mb-1">URL Gambar/Thumbnail (Opsional)</label>
                    <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors" placeholder="https://contoh.com/gambar.jpg">
                    @error('image_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="space-y-4 pt-4 border-t border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 border-b border-gray-100 pb-2">Lokasi Pekerjaan</h3>
            
            <div class="flex items-center gap-2 mb-4 bg-blue-50 p-4 rounded-xl border border-blue-100">
                <input type="checkbox" id="is_remote" name="is_remote" value="1" x-model="isRemote" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                <label for="is_remote" class="font-bold text-blue-800 cursor-pointer flex-1">💻 Pekerjaan ini bisa dilakukan dari mana saja (Remote)</label>
            </div>

            <div x-show="!isRemote" x-transition class="space-y-4 p-4 border border-gray-200 rounded-xl bg-gray-50">
                <p class="text-sm text-gray-500 mb-2"><i class="fas fa-info-circle text-blue-500 mr-1"></i> Silakan isi lokasi fisik di mana pekerjaan harus dilakukan.</p>
                <div>
                    <label for="addressLine" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                    <input type="text" id="addressLine" name="addressLine" value="{{ old('addressLine') }}" :required="!isRemote" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors">
                    @error('addressLine') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-1">Kota/Kabupaten</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" :required="!isRemote" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors">
                        @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="province" class="block text-sm font-semibold text-gray-700 mb-1">Provinsi</label>
                        <input type="text" id="province" name="province" value="{{ old('province') }}" :required="!isRemote" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors">
                        @error('province') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
            <a href="{{ route('home') }}" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-xl transition-colors">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-md transition-all">Terbitkan Lowongan</button>
        </div>
    </form>
</div>
@endsection
