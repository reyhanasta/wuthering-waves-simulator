<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<x-layouts.header></x-layouts.header>

<body class="font-sans antialiased dark:bg-black dark:text-white/50">
    {{ $slot }}
</body>

</html>
<x-layouts.script></x-layouts.script>
