<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/appIcon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/appIcon.png') }}">
    <script>
        (function () {
            try {
                var root = document.documentElement;
                var storedTheme = localStorage.getItem('pm-theme');
                var isDark = storedTheme === 'dark'
                    || (storedTheme !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);

                root.classList.toggle('dark', isDark);
                root.style.colorScheme = isDark ? 'dark' : 'light';
            } catch (_error) {
                // Ignore failures and keep default theme.
            }
        }());
    </script>
    @routes
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
@inertia
</body>
</html>
