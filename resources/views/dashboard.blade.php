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

        <div class="rounded-3xl bg-gradient-to-r from-pink-500 via-fuchsia-500 to-pink-400 p-8 text-white shadow-lg">

            <h2 class="text-3xl font-bold">
                Halo, Admin 👋
            </h2>

            <p class="mt-2 text-pink-100">
                Selamat datang kembali di Website Photobooth Online.
            </p>

        </div>

            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                <!-- User -->
                <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-zinc-500 text-sm">
                                Total User
                            </p>

                            <h2 class="text-4xl font-bold mt-3 text-zinc-800">
                                {{ $stats['users'] }}
                            </h2>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-pink-100 flex items-center justify-center text-3xl">
                        👤
                    </div>
                </div>
            </div>
        </div>

        <!-- Frame -->
        <div class="bg-white rounded-3xl shadow-sm border border-purple-100 p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-500 text-sm">
                        Total Frame
                    </p>

                    <h2 class="text-4xl font-bold mt-3 text-zinc-800">
                        {{ $stats['frames'] }}
                    </h2>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center text-3xl">
                    🖼
                </div>
            </div>
        </div>

        <!-- Photobox -->
        <div class="bg-white rounded-3xl shadow-sm border border-blue-100 p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-500 text-sm">
                        Total Photobox
                    </p>

                    <h2 class="text-4xl font-bold mt-3 text-zinc-800">
                        {{ $stats['results'] }}
                    </h2>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-3xl">
                    📸
                </div>
            </div>
        </div>

        <!-- Download -->
        <div class="bg-white rounded-3xl shadow-sm border border-green-100 p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-500 text-sm">
                        Total Download
                    </p>

                    <h2 class="text-4xl font-bold mt-3 text-zinc-800">
                        {{ \App\Models\Downloads::count() }}
                    </h2>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center text-3xl">
                    ⬇
                </div>
            </div>
        </div>

    </div>

</div>

@else
            <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-3xl border border-pink-100 shadow-sm p-8">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                    <div>

                        <h2 class="text-3xl font-bold text-pink-600">
                            📸 Mulai Photobox
                        </h2>

                        <p class="mt-3 text-zinc-600 max-w-xl">
                            Pilih frame favoritmu dan buat photobox sekarang.
                        </p>

                </div>

                <div>

                    <a href="{{ route('photoboxresults.index') }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-pink-500 px-6 py-3 font-semibold text-white hover:bg-pink-600 transition">

                            ✨ Buat Photobox

                    </a>

                </div>

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