@extends('layouts.frontend')

@section('content')
<div class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-6">
        
        <div class="text-center mb-16">
            <h1 class="text-5xl font-black text-gray-900 mb-4">Galeri Kenangan ✨</h1>
            <p class="text-xl text-gray-600">Lihat hasil karya seru dari komunitas kami!</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @forelse($results as $result)
                <div class="bg-white p-3 rounded-3xl shadow-md hover:shadow-2xl transition duration-500 group overflow-hidden">
                    <div class="relative overflow-hidden rounded-2xl h-80">
                        <img src="{{ asset('storage/'.$result->file_path) }}" 
                             alt="Photobooth Result" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                            <a href="{{ asset('storage/'.$result->file_path) }}" target="_blank" class="bg-white px-6 py-2 rounded-full font-bold text-gray-800">
                                Lihat
                            </a>
                        </div>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-sm text-gray-400">{{ $result->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20">
                    <p class="text-2xl text-gray-500">Belum ada hasil photobox yang diunggah. Yuk, mulai buat sekarang!</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $results->links() }}
        </div>
    </div>
</div>
@endsection