<nav class="fixed top-0 left-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-pink-100">

    <div class="max-w-7xl mx-auto px-8 py-5 flex items-center justify-between">

        <!-- Logo -->

        <a href="/" class="flex items-center gap-3">

            <div class="w-12 h-12 rounded-2xl bg-pink-500 flex items-center justify-center text-white text-2xl">

                📸

            </div>

            <div>

                <h1 class="text-3xl font-black text-pink-500">

                    Photobooth

                </h1>

            </div>

        </a>

        <!-- Menu -->

        <div class="hidden lg:flex items-center gap-10">
            <a href="/" class="text-gray-700 font-semibold hover:text-pink-500 transition">
                Home
            </a>
            <a href="{{ route('frames') }}" class="text-gray-700 font-semibold hover:text-pink-500 transition">
                Explore Frame
            </a>
            <a href="{{ route('gallery') }}" class="text-gray-700 hover:text-pink-500 font-bold transition">
                Galeri
            </a>
        </div>

            @guest

            <a href="{{ route('login') }}"
                class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-full shadow-lg">

                Login

            </a>

            @else

            <a href="{{ route('photoboxresults.index') }}"
                class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-full shadow-lg">

                Dashboard

            </a>

            @endguest

        </div>

    </div>

</nav>