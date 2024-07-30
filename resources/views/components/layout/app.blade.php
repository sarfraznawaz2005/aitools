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

<x-layout.header :title="$title ?? 'AiTools'"/>

<div class="flex">
    <x-layout.sidebar :title="$title ?? 'AiTools'"/>

    <!-- main content start -->
    <div class="flex-1 pt-5 px-4 sm:px-6 md:px-8 text-gray-600 dark:text-neutral-200">
        {{ $slot }}
    </div>
</div>
<!-- main content end -->

<script>
    document.body.addEventListener('htmx:configRequest', (event) => {
        event.detail.headers['X-CSRF-Token'] = '{{ csrf_token() }}';
    })
</script>

</body>

</html>
