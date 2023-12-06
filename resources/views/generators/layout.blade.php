<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=geo:400&display=swap" rel="stylesheet" />

    <link rel="icon" href="/bsite.svg">

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    @yield('content')
</body>

</html>