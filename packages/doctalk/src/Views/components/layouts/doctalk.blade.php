<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'DocTalk' }}</title>

    <link rel="stylesheet" href="{{ asset('vendor/doctalk/assets/doctalk.css') }}"/>
    <script defer src="{{ asset('vendor/doctalk/assets/doctalk.js') }}"></script>

    @yield('styles')
</head>
<body>

<button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

<div class="chat-container">

    <div class="sidebar" id="sidebar">
        {{ $sidebar ?? '' }}
    </div>

    <div class="main-content" id="main-content">
        {{ $slot }}
    </div>
</div>

@yield('scripts')

</body>
</html>
