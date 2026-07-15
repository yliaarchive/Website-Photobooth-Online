@extends('layouts.frontend')

@section('content')
<div class="bg-gray-50 py-20 min-h-screen">
    <div class="max-w-7xl mx-auto px-6">
        
        <div class="text-center mb-16">
            <h1 class="text-5xl font-black text-gray-900 mb-4">Galeri Kenangan ✨</h1>
            <p class="text-xl text-gray-600">Lihat hasil karya seru dari komunitas kami!</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @forelse($results as $result)
                <div class="bg-white p-3 rounded-3xl shadow-md hover:shadow-2xl transition duration-500 group overflow-hidden">
                    <div class="relative overflow-hidden rounded-2xl h-80 bg-gray-100 flex items-center justify-center">
                        
                        <img src="{{ asset('storage/'.$result->result_file) }}" 
                             alt="Photobooth Result" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col items-center justify-center gap-3">
                            
                            <a href="{{ asset('storage/'.$result->result_file) }}" target="_blank" class="bg-white px-6 py-2 rounded-full font-bold text-gray-800 hover:bg-gray-100 transition shadow-lg text-sm w-32 text-center">
                                👀 Preview
                            </a>

                            <a href="{{ asset('storage/'.$result->result_file) }}" 
                               download="Photobox-Kenangan-{{ $result->id }}.png" 
                               class="bg-pink-500 px-6 py-2 rounded-full font-bold text-white hover:bg-pink-600 transition shadow-lg text-sm w-32 text-center flex items-center justify-center gap-2">
                                
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Simpan
                            </a>

                        </div>
                    </div>
                    <div class="p-4 text-center border-t border-gray-50 mt-2">
                        <p class="text-sm font-medium text-gray-400">{{ $result->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="text-6xl mb-4">🖼️</div>
                    <p class="text-2xl font-bold text-gray-500">Belum ada hasil photobox.</p>
                    <p class="text-gray-400 mt-2">Yuk, jadilah yang pertama membuat kenangan!</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $results->links() }}
        </div>
    </div>
</div>
@endsection