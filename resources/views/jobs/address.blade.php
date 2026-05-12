@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <a href="{{ route('jobs.show', $job->jobID) }}" class="text-blue-600 hover:underline mb-4 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Detail Pekerjaan</a>
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Detail Alamat</h2>
            
            <div class="w-full h-64 bg-gray-200 rounded-xl mb-6 flex items-center justify-center">
                <i class="fas fa-map-marker-alt text-4xl text-gray-400"></i>
                <span class="ml-2 text-gray-500 font-medium">Map Placeholder</span>
            </div>

            <div class="bg-blue-50 p-6 rounded-xl">
                <h4 class="font-bold text-gray-900 mb-2">Alamat Lengkap</h4>
                <p class="text-gray-700 mb-1">{{ $job->location->addressLine }}</p>
                <p class="text-gray-700">{{ $job->location->city }}, {{ $job->location->province }} {{ $job->location->postalCode }}</p>
            </div>
        </div>
    </div>
</div>
@endsection