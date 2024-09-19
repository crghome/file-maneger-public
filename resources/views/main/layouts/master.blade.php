<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="description" content="@yield('description', config('app.title'))">
        <meta name="keywords" content="@yield('keywords', config('app.title'))">
        <meta name="author" content="@yield('author', config('app.name'))">
        <title>@yield('title', config('app.name'))</title>
        <meta name="_token" content="{!! csrf_token() !!}" />
        <meta name="csrf-token" content="{!! csrf_token() !!}" />

        <link rel="apple-touch-icon" sizes="180x180" href="/img/icons/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/img/icons/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/img/icons/favicon/favicon-16x16.png">
        <link rel="manifest" href="/img/icons/favicon/site.webmanifest">
        <link rel="mask-icon" href="/img/icons/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <link href="{{ asset('css/fontawesome.min.css') }}" rel="stylesheet">
        
        @vite(['resources/sass/main.scss'])
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div id="app-vue-main">
            @yield('contents')
            <vue-modal></vue-modal>
        </div>

        @vite(['resources/js/app.js'])
    </body>
</html>
