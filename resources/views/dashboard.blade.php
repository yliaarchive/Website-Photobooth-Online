<x-layouts::app :title="__('Dashboard')">
    @php
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';
        
        $stats = $isAdmin ? [
            'users' => \App\Models\User::count(),
            'frames' => \App\Models\PhotoFrames::count(),
            'results' => \App\Models\PhotoboxResults::count(),
            'downloads' => \App\Models\Downloads::count(),
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
        <div class="rounded-3xl bg-gradient-to-r from-pink-400 via-fuchsia-400 to-purple-400 p-8 text-white shadow-xl">
            <h2 class="text-3xl font-bold">
                Halo, Admin 👋
            </h2>

            <p class="mt-2 text-pink-50">
                Selamat datang kembali di Website Photobooth Online.
            </p>
        </div>

            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                <!-- User -->
                <div class="bg-white rounded-3xl border border-pink-100 border-t-4 border-t-pink-400 p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-500 font-medium">
                                Total User
                             </p>

                            <h2 class="text-5xl font-bold mt-2 text-pink-600">
                                {{ $stats['users'] }}
                            </h2>
                        </div>

                        <div class="w-16 h-16 rounded-2xl bg-pink-100 flex items-center justify-center text-3xl shadow-sm">
                            👤
                        </div>
                    </div>
                </div>

            <!-- Frame -->
            <div class="bg-white rounded-3xl border border-fuchsia-100 border-t-4 border-t-fuchsia-400 p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 font-medium">
                            Total Frame
                        </p>

                        <h2 class="text-5xl font-bold mt-2 text-fuchsia-600">
                            {{ $stats['frames'] }}
                        </h2>
                    </div>

                    <div class="w-16 h-16 rounded-2xl bg-fuchsia-100 flex items-center justify-center text-3xl shadow-sm">
                        🖼
                    </div>
                </div>
            </div>

            <!-- Photobox -->
            <div class="bg-white rounded-3xl border border-purple-100 border-t-4 border-t-purple-400 p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 font-medium">
                            Total Photobox
                        </p>

                        <h2 class="text-5xl font-bold mt-2 text-purple-600">
                            {{ $stats['results'] }}
                        </h2>
                    </div>

                    <div class="w-16 h-16 rounded-2xl bg-purple-100 flex items-center justify-center text-3xl shadow-sm">
                        📸
                    </div>
                </div>
            </div>

            <!-- Download -->
            <div class="bg-white rounded-3xl border border-violet-100 border-t-4 border-t-violet-400 p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 font-medium">
                            Total Download
                        </p>

                        <h2 class="text-5xl font-bold mt-2 text-violet-600">
                            {{ $stats['downloads'] }}
                        </h2>
                    </div>

                    <div class="w-16 h-16 rounded-2xl bg-violet-100 flex items-center justify-center text-3xl shadow-sm">
                        ⬇
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