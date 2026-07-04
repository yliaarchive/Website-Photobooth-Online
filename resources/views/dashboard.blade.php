<x-layouts::app :title="__('Dashboard')">
    @php
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';
        
        $stats = $isAdmin ? [
            'users' => \App\Models\User::count(),
            'frames' => \App\Models\PhotoFrames::count(),
            'results' => \App\Models\PhotoboxResults::count(),
            'recent' => \App\Models\PhotoboxResults::with(['user', 'frame'])->latest()->take(5)->get()
        ] : null;

        $myResults = !$isAdmin ? \App\Models\PhotoboxResults::with('frame')->where('user_id', $user->id)->latest()->take(10)->get() : collect();
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-8 p-6">
        <div>
            <h1 class="text-3xl font-bold text-zinc-800 dark:text-white font-sans tracking-tight">Dashboard</h1>
            <p class="text-zinc-500 mt-1">Selamat datang kembali, <span class="text-pink-500 font-semibold">{{ $user->name }}</span>!</p>
        </div>

        @if($isAdmin)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach(['Total Pengguna' => [$stats['users'], 'pink'], 'Total Frame Aktif' => [$stats['frames'], 'purple'], 'Photobox Tergenerate' => [$stats['results'], 'blue']] as $label => $data)
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm border-t-4 border-t-{{$data[1]}}-400 transition hover:shadow-md">
                        <p class="text-sm font-medium text-zinc-500 uppercase tracking-wider">{{ $label }}</p>
                        <p class="text-4xl font-bold text-zinc-800 mt-2">{{ $data[0] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="bg-white rounded-2xl border border-zinc-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold mb-4 text-zinc-800">Aktivitas Terbaru</h3>
                <div class="space-y-4">
                    @foreach($stats['recent'] as $r)
                        <div class="flex justify-between border-b pb-3 border-zinc-100 last:border-0 last:pb-0">
                            <span class="font-medium text-zinc-700">{{ $r->user->name ?? 'User' }}</span>
                            <span class="text-zinc-400 text-sm italic">{{ $r->frame->frame_name ?? 'Frame' }} • {{ $r->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="relative overflow-hidden rounded-2xl border border-pink-200 bg-pink-50 p-8 shadow-sm">
                <h2 class="text-2xl font-bold text-pink-600 mb-3">Mulai Buat Photobox Pertamamu!</h2>
                <p class="text-zinc-700 mb-4">Ikuti 3 langkah mudah untuk hasil foto estetik:</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center gap-3"><div class="h-8 w-8 rounded-full bg-pink-200 flex items-center justify-center font-bold text-pink-700">1</div><p>Buka <b>Photobox Results</b>.</p></div>
                    <div class="flex items-center gap-3"><div class="h-8 w-8 rounded-full bg-pink-200 flex items-center justify-center font-bold text-pink-700">2</div><p>Pilih frame & upload foto.</p></div>
                    <div class="flex items-center gap-3"><div class="h-8 w-8 rounded-full bg-pink-200 flex items-center justify-center font-bold text-pink-700">3</div><p>Klik <b>Generate Magic</b>.</p></div>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-bold mb-4 text-zinc-800">Riwayat Fotomu</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($myResults as $r)
                        <div class="border border-zinc-200 rounded-2xl p-2 bg-white hover:shadow-lg transition">
                            <img src="{{ asset('storage/' . $r->result_file) }}" class="w-full h-48 object-cover rounded-xl">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>