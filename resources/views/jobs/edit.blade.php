@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ isRemote: {{ old('is_remote', $job->is_remote) ? 'true' : 'false' }} }">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Edit Lowongan</h1>
        <p class="text-gray-500 mt-1">Ubah detail pekerjaan micro-tasking Anda</p>
    </div>

    <form action="{{ route('jobs.update', $job->jobID) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @csrf
        @method('PUT')

        {{-- Section 1: Job Information --}}
        <div class="p-8 space-y-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-briefcase text-blue-600 text-sm"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Informasi Pekerjaan</h3>
            </div>
            
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Pekerjaan</label>
                <input type="text" id="title" name="title" value="{{ old('title', $job->title) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-gray-50 focus:bg-white" placeholder="Contoh: Desain Logo Kedai Kopi">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="categoryID" class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori</label>
                <select id="categoryID" name="categoryID" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-gray-50 focus:bg-white">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->categoryID }}" {{ old('categoryID', $job->categoryID) == $category->categoryID ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categoryID') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Lengkap</label>
                <textarea id="description" name="description" rows="5" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-gray-50 focus:bg-white" placeholder="Jelaskan secara detail apa yang harus dilakukan pekerja...">{{ old('description', $job->description) }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="payAmount" class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah Bayaran (Rp)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-bold text-sm">Rp</span>
                    </div>
                    <input type="number" id="payAmount" name="payAmount" value="{{ old('payAmount', $job->payAmount) }}" required min="0" class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-gray-50 focus:bg-white" placeholder="150000">
                </div>
                @error('payAmount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- ====== Image Upload Area (Vanilla JS) ====== --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Thumbnail Pekerjaan (Opsional)</label>
                <div class="relative">
                    <div id="dropZone" class="border-2 border-dashed border-gray-300 hover:border-blue-400 rounded-2xl p-6 text-center transition-all duration-200 cursor-pointer bg-gray-50 hover:bg-blue-50/50 min-h-[180px] flex items-center justify-center">
                        
                        {{-- Upload Prompt (hidden if existing image) --}}
                        <div id="uploadPrompt" class="{{ $job->image_url ? 'hidden' : '' }}">
                            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-cloud-upload-alt text-blue-600 text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Klik atau seret gambar ke sini</p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF, WEBP — Maks. 2MB</p>
                        </div>

                        {{-- Image Preview --}}
                        <div id="previewContainer" class="{{ $job->image_url ? '' : 'hidden' }} relative w-full">
                            @if($job->image_url)
                                <img id="imagePreview" class="max-h-52 mx-auto rounded-xl object-cover shadow-md" 
                                     src="{{ str_starts_with($job->image_url, 'http') ? $job->image_url : asset('storage/' . $job->image_url) }}" 
                                     alt="Current Thumbnail">
                            @else
                                <img id="imagePreview" class="max-h-52 mx-auto rounded-xl object-cover shadow-md" src="" alt="Preview">
                            @endif
                            <button type="button" id="removeImageBtn" class="absolute top-2 right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg transition-colors">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                            <p id="fileName" class="text-xs text-gray-500 mt-3 font-medium">{{ $job->image_url ? 'Thumbnail saat ini — pilih file baru untuk mengganti' : '' }}</p>
                        </div>
                    </div>
                    <input type="file" id="thumbnailUpload" name="image" accept="image/*" class="hidden">
                </div>
                @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Section 2: Location --}}
        <div class="p-8 border-t border-gray-100 space-y-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-emerald-600 text-sm"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Lokasi Pekerjaan</h3>
            </div>
            
            <div class="flex items-center gap-3 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-100">
                <input type="checkbox" id="is_remote" name="is_remote" value="1" x-model="isRemote" class="w-5 h-5 text-blue-600 rounded-lg focus:ring-blue-500 border-gray-300">
                <label for="is_remote" class="font-bold text-blue-800 cursor-pointer flex-1 flex items-center gap-2">
                    <span class="text-lg">💻</span> Pekerjaan ini bisa dilakukan dari mana saja (Remote)
                </label>
            </div>

            <div x-show="!isRemote" x-transition.duration.300ms class="space-y-4 p-5 border border-gray-200 rounded-xl bg-gray-50/70">
                <p class="text-sm text-gray-500 flex items-center gap-2"><i class="fas fa-info-circle text-blue-500"></i> Silakan isi lokasi fisik di mana pekerjaan harus dilakukan.</p>
                <div>
                    <label for="addressLine" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                    <input type="text" id="addressLine" name="addressLine" value="{{ old('addressLine', $job->location->addressLine ?? '') }}" :required="!isRemote" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white">
                    @error('addressLine') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-1">Kota/Kabupaten</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $job->location->city ?? '') }}" :required="!isRemote" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white">
                        @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="province" class="block text-sm font-semibold text-gray-700 mb-1">Provinsi</label>
                        <input type="text" id="province" name="province" value="{{ old('province', $job->location->province ?? '') }}" :required="!isRemote" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white">
                        @error('province') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-4">
            <a href="{{ route('home') }}" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-200 rounded-xl transition-colors">Batal</a>
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-600/25 transition-all flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('thumbnailUpload');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const removeBtn = document.getElementById('removeImageBtn');
    const fileNameEl = document.getElementById('fileName');

    // Click the drop zone to open file picker
    dropZone.addEventListener('click', function(e) {
        if (e.target.closest('#removeImageBtn')) return;
        fileInput.click();
    });

    // File selected via input
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            showPreview(this.files[0]);
        }
    });

    // Drag over — visual feedback
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('border-blue-500', 'bg-blue-50', 'scale-[1.01]');
        this.classList.remove('border-gray-300', 'bg-gray-50');
    });

    // Drag leave — reset visual
    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('border-blue-500', 'bg-blue-50', 'scale-[1.01]');
        this.classList.add('border-gray-300', 'bg-gray-50');
    });

    // Drop — handle file
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('border-blue-500', 'bg-blue-50', 'scale-[1.01]');
        this.classList.add('border-gray-300', 'bg-gray-50');

        const files = e.dataTransfer.files;
        if (files && files[0] && files[0].type.startsWith('image/')) {
            fileInput.files = files;
            showPreview(files[0]);
        }
    });

    // Remove button — clear preview and input
    removeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        clearPreview();
    });

    function showPreview(file) {
        const url = URL.createObjectURL(file);
        imagePreview.src = url;
        fileNameEl.textContent = file.name + ' (' + formatSize(file.size) + ')';

        uploadPrompt.classList.add('hidden');
        previewContainer.classList.remove('hidden');

        // Animate in
        previewContainer.style.opacity = '0';
        previewContainer.style.transform = 'scale(0.95)';
        requestAnimationFrame(function() {
            previewContainer.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            previewContainer.style.opacity = '1';
            previewContainer.style.transform = 'scale(1)';
        });
    }

    function clearPreview() {
        fileInput.value = '';
        imagePreview.src = '';
        fileNameEl.textContent = '';
        previewContainer.classList.add('hidden');
        uploadPrompt.classList.remove('hidden');
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }
});
</script>
@endsection
