@php
    use App\Models\PhotoFrames;

    $frames = PhotoFrames::with('frameCategories')
        ->latest()
        ->get();
@endphp

@extends('layouts.frontend')

@section('content')

<section class="relative min-h-screen overflow-hidden bg-gradient-to-br from-pink-50 via-white to-purple-50">

    <!-- Background Blur -->
    <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-pink-200 opacity-40 blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 rounded-full bg-purple-200 opacity-40 blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-8 pt-36 pb-20">

        <!-- Judul -->
        <div class="text-center mb-16">

            <span class="inline-block px-6 py-2 rounded-full bg-pink-100 text-pink-600 font-semibold">

                ✨ Explore Collection

            </span>

            <h1 class="mt-6 text-5xl font-black text-gray-800">

                Explore Frame

            </h1>

            <p class="mt-4 text-lg text-gray-500">

                Pilih frame favoritmu sebelum membuat photobox.

            </p>

        </div>

        <!-- List Frame -->

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            @forelse($frames as $frame)

                <div class="bg-white rounded-3xl shadow-lg overflow-hidden hover:-translate-y-2 hover:shadow-2xl transition duration-300">

                    <!-- Gambar -->

                    <div class="relative h-80 overflow-hidden">

                        <img
                            src="{{ asset('storage/'.$frame->gambar_frame) }}"
                            alt="{{ $frame->nama_frame }}"
                            class="w-full h-full object-cover hover:scale-110 transition duration-500">

                    </div>

                    <!-- Isi -->

                    <div class="p-6">

                        <h2 class="text-2xl font-bold text-gray-800">

                            {{ $frame->nama_frame }}

                        </h2>

                        <div class="mt-3">

                            <span class="px-3 py-1 rounded-full bg-pink-100 text-pink-600 text-sm">

                                {{ $frame->frameCategories->nama_kategori ?? 'Photobooth' }}

                            </span>

                        </div>

                        <p class="mt-4 text-gray-500">

                            {{ $frame->tema ?: 'Frame photobooth yang lucu dan estetik.' }}

                        </p>

                        <a
                            href="{{ route('photobox.create', $frame->id) }}"
                            class="mt-6 flex justify-center rounded-full bg-pink-500 py-3 font-semibold text-white hover:bg-pink-600 transition">

                            Gunakan Frame

                        </a>

                    </div>

                </div>

            @empty

                <div class="col-span-4 text-center py-20">

                    <div class="text-7xl">

                        📸

                    </div>

                    <h2 class="mt-5 text-3xl font-bold text-pink-500">

                        Belum Ada Frame

                    </h2>

                    <p class="mt-3 text-gray-500">

                        Admin belum menambahkan frame.

                    </p>

                </div>

            @endforelse

        </div>

    </div>

</section>

@endsection