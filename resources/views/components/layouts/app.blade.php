<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ? $title . ' - AiTools' : 'AiTools' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ubuntu:300,400,500,700" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-50 dark:bg-neutral-900">

@livewire('header', ['title' => $title ?? 'AiTools'])

<div class="flex">
    <x-layouts.sidebar/>

    <!-- main content start -->
    <div class="flex-1 pt-5 px-4 text-gray-600 dark:text-neutral-200">

        <x-flash />

        {{ $slot }}
    </div>
    <!-- main content end -->
</div>

</body>

</html>
