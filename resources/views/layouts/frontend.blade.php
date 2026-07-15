<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Photobooth Online</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="bg-pink-50 text-gray-700">
    
    @include('frontend.partials.navbar')

    <main>

        @yield('content')

    </main>

    @include('frontend.partials.footer')

</body>

</html>