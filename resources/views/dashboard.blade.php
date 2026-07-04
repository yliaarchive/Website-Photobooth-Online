<x-layouts::app :title="__('Dashboard')">
    @php
        $user = auth()->user();
        $myResults = \App\Models\PhotoboxResults::with('frame')
                        ->where('user_id', $user->id)
                        ->latest()
                        ->take(10)
                        ->get();
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-6 p-2">
        <!-- Header -->
        <div class="mb-2">
            <h1 class="text-3xl font-bold text-zinc-800 dark:text-white font-sans">Dashboard</h1>
            <p class="text-zinc-500 mt-1">Selamat datang kembali, <span class="text-pink-500 font-semibold">{{ $user->name }}</span>!</p>
        </div>

        <!-- Banner Panduan (Identitas Visual: Pink Pastel & User Friendly) -->
        <div class="relative overflow-hidden rounded-2xl border border-pink-200 bg-pink-50 p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-pink-600 mb-3">Mulai Buat Photobox Pertamamu!</h2>
            <p class="text-zinc-700 mb-4">Ikuti 3 langkah mudah untuk hasil foto estetik:</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-start gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-pink-200 text-pink-700 font-bold">1</span>
                    <span class="text-zinc-700">Buka menu <b>Photobox Results</b> di samping kiri.</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-pink-200 text-pink-700 font-bold">2</span>
                    <span class="text-zinc-700">Pilih frame favoritmu dan unggah fotomu.</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-pink-200 text-pink-700 font-bold">3</span>
                    <span class="text-zinc-700">Sesuaikan posisi di Editor lalu klik <b>Generate Magic</b>.</span>
                </div>
            </div>
        </div>

        <!-- Riwayat Foto (Aesthetic Grid) -->
        <div class="flex-1">
            <h3 class="text-xl font-bold mb-4 text-zinc-800 dark:text-white">Riwayat Fotomu</h3>
            
            @if($myResults->isEmpty())
                <div class="flex flex-col items-center justify-center h-64 border-2 border-dashed border-zinc-200 rounded-2xl bg-zinc-50">
                    <flux:icon.camera class="size-12 text-zinc-300 mb-3" />
                    <span class="text-zinc-400 font-medium">Belum ada foto yang dibuat. Ayo berkarya!</span>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-5">
                    @foreach ($myResults as $result)
                        <div class="group border border-zinc-200 rounded-2xl p-3 bg-white shadow-sm hover:border-pink-300 transition-all duration-300">
                            <img src="{{ asset('storage/' . $result->result_file) }}" class="w-full h-56 rounded-xl object-cover shadow-inner">
                            <div class="mt-3 px-1">
                                <p class="text-sm font-semibold text-zinc-800 truncate">{{ $result->frame->frame_name ?? 'Custom Frame' }}</p>
                                <p class="text-xs text-zinc-400">{{ $result->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>