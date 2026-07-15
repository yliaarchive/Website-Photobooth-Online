@extends('layouts.frontend')

@section('content')

<section class="relative overflow-hidden bg-gradient-to-br from-pink-50 via-white to-pink-100 pb-20">
    <div class="absolute w-96 h-96 rounded-full bg-pink-200 opacity-40 blur-3xl -top-20 -left-20"></div>
    <div class="absolute w-96 h-96 rounded-full bg-purple-200 opacity-30 blur-3xl bottom-0 right-0"></div>

    <div class="max-w-7xl mx-auto px-8 pt-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            
            <div>
                <span class="bg-pink-100 text-pink-600 px-5 py-2 rounded-full font-semibold shadow-sm">
                    ✨ Website Photobooth Online
                </span>
                
                <h1 class="mt-8 text-6xl font-black leading-tight text-gray-900">
                    Buat Kenangan
                    <br>
                    <span class="text-pink-500">
                        Dalam Satu Klik.
                    </span>
                </h1>
                
                <p class="mt-6 text-xl text-gray-600 leading-9">
                    Pilih frame favoritmu, upload foto, dan download hasil photobooth dengan kualitas terbaik untuk mengabadikan momen serumu!
                </p>
                
                <div class="flex flex-wrap gap-5 mt-10">
                    <a href="{{ route('frames') }}" 
                        class="px-8 py-4 rounded-full bg-pink-500 hover:bg-pink-600 text-white font-bold shadow-xl hover:-translate-y-1 transition duration-300">
                        📸 Buat Photobox
                    </a>
    
                    <a href="#gallery" 
                        class="px-8 py-4 rounded-full border-2 border-pink-500 text-pink-500 hover:bg-pink-50 font-bold transition duration-300">
                        Lihat Galeri
                    </a>
                </div>
            </div>

            <div class="flex justify-center relative">
                <div class="w-[450px] h-[450px] rounded-full bg-gradient-to-br from-pink-300 to-purple-300 flex items-center justify-center text-[160px] shadow-[0_20px_50px_rgba(236,72,153,0.3)] animate-float border-8 border-white">
                    📸
                </div>     
            </div>

        </div>
    </div>
</section>

<section class="relative py-24 bg-white rounded-t-[3rem] shadow-[0_-10px_40px_rgba(0,0,0,0.03)] border-t border-pink-100">
    <div class="max-w-7xl mx-auto px-8">
        
        <div class="text-center mb-16">
            <span class="inline-block px-6 py-2 rounded-full bg-pink-100 text-pink-600 font-semibold mb-4">
                ✨ Explore Collection
            </span>
            <h2 class="text-4xl font-black text-gray-800">
                Pilih Frame Favoritmu
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($frames ?? [] as $frame)
                <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(236,72,153,0.08)] border border-pink-50 overflow-hidden hover:-translate-y-2 hover:shadow-[0_10px_30px_rgba(236,72,153,0.2)] transition duration-300 group">
                    
                    <div class="relative h-80 overflow-hidden bg-pink-50">
                        <img src="{{ asset('storage/'.$frame->gambar_frame) }}" alt="{{ $frame->nama_frame }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    </div>
                    
                    <div class="p-6 text-center">
                        <h3 class="text-2xl font-bold text-gray-800">{{ $frame->nama_frame }}</h3>
                        <div class="mt-3">
                            <span class="px-3 py-1 rounded-full bg-pink-100 text-pink-600 text-sm font-medium">
                                {{ $frame->frameCategories->nama_kategori ?? 'Photobooth' }}
                            </span>
                        </div>
                        <a href="{{ route('login') }}" class="mt-6 flex justify-center rounded-full bg-pink-500 hover:bg-pink-600 py-3 font-semibold text-white transition shadow-md hover:shadow-lg">
                            Gunakan Frame
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-4 text-center py-20 bg-pink-50/50 rounded-3xl border-2 border-dashed border-pink-200">
                    <div class="text-6xl mb-4 animate-bounce">😿</div>
                    <h3 class="text-2xl font-bold text-pink-500">Belum Ada Frame</h3>
                    <p class="mt-2 text-gray-500">Admin belum menambahkan koleksi frame baru nih.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>

@endsection