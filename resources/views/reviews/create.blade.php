@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Beri Ulasan</h1>
        <p class="text-gray-500 mt-1">Bagikan pengalaman Anda bekerja sama dengan {{ $targetUser->fullName }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-6 shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50 flex items-center gap-4">
            <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl font-bold">
                {{ substr($targetUser->fullName, 0, 1) }}
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-0.5">Pekerjaan: {{ $contract->job->title }}</p>
                <h3 class="text-xl font-bold text-gray-900">{{ $targetUser->fullName }}</h3>
            </div>
        </div>

        <form action="{{ route('reviews.store', $contract->contractID) }}" method="POST" class="p-6 sm:p-8" x-data="{ rating: 0, hoverRating: 0 }">
            @csrf

            <div class="mb-8 text-center">
                <label class="block text-lg font-bold text-gray-900 mb-4">Seberapa puas Anda dengan hasil kerja/kerjasama ini?</label>
                <div class="flex items-center justify-center gap-2">
                    <template x-for="i in 5">
                        <button type="button" 
                                @click="rating = i" 
                                @mouseenter="hoverRating = i" 
                                @mouseleave="hoverRating = 0"
                                class="focus:outline-none transition-transform hover:scale-110">
                            <i class="fas fa-star text-4xl transition-colors duration-200"
                               :class="(hoverRating >= i || rating >= i) ? 'text-yellow-400' : 'text-gray-200'"></i>
                        </button>
                    </template>
                </div>
                <input type="hidden" name="rating" x-model="rating" required>
                @error('rating') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="mb-8">
                <label for="comment" class="block text-sm font-bold text-gray-700 mb-2">Pesan Ulasan (Opsional)</label>
                <textarea id="comment" name="comment" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors resize-none" placeholder="Tuliskan komentar membangun mengenai pengalaman Anda..."></textarea>
                @error('comment') <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('notifications.index') }}" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-xl transition-colors">Nanti Saja</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-md shadow-blue-600/20 transition-all flex items-center gap-2" :disabled="rating === 0" :class="{ 'opacity-50 cursor-not-allowed': rating === 0 }">
                    <i class="fas fa-paper-plane"></i> Kirim Ulasan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
