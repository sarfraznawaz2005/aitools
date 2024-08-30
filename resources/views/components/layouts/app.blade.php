<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ? $title . ' - AiTools' : 'AiTools' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased bg-gray-50 dark:bg-neutral-900">

<x-autoloading/>
<x-page-expired/>

<div class="text-gray-600">
    <livewire:general.header title="{{$title ?? ''}}"/>
    <livewire:general.offline/>

    <x-loading/>
    <x-toast/>

    {{ $slot }}
</div>

@livewireScriptConfig

</body>
</html>
