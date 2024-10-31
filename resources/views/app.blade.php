<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title inertia>{{ config('app.name', 'UDH') }}</title>

    <meta name="description" content="Sistema de atención para tickets de soporte informático de la UDH.">
    <meta name="keywords"
        content="Soporte UDH, Incidencias UDH, Soporte tecnico, Averias UDH, Universidad de Huanuco, Huanuco, UDH">
    <meta name="author" content="Laboratorio de Transformacion Digital">
    <meta name="theme-color" content="#2EBAA0">

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Satsi UDH') }}">
    <meta property="og:title" content="{{ config('app.name') }}">
    <meta property="og:description" content="Sistema de atención para tickets de soporte informático de la UDH.">
        <meta property="og:image" content="{{ asset('img/og_image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:type" content="website">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        crossorigin="anonymous" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
    <script defer></script>
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
