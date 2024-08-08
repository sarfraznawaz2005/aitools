<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ? $title . ' - AiTools' : 'AiTools' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased bg-gray-50 dark:bg-neutral-900 h-screen">

<livewire:general.header title="{{$title ?? 'AiTools'}}"/>

<div class="flex h-screen">

    <!-- main content start -->
    <div class="flex-1 text-gray-600 dark:text-neutral-200">
        <livewire:general.offline/>

        <x-flash/>

        {{ $slot }}
    </div>
    <!-- main content end -->
</div>

@livewireScriptConfig

<script>
    window.addEventListener('toast-message', function (event) {
        const style = event.detail[0].style;
        const message = event.detail[0].message;

        const notificationType = (style === 'success' || style === 'error' || style === 'warning') ? style : 'info';

        window.notyf.open({
            type: notificationType,
            message: message
        });
    });
</script>

</body>
</html>
