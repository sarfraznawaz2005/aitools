<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AiTools</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased bg-gray-50 dark:bg-neutral-900 h-screen">

<x-autoloading/>

<div class="flex h-screen">

    <!-- main content start -->
    <div class="flex-1 text-gray-600 dark:text-neutral-200 mainSection">
        <x-loading/>
        <x-toast/>

        {{ $slot }}
    </div>
    <!-- main content end -->
</div>

@livewireScriptConfig

</body>
</html>
