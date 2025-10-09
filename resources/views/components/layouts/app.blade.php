{{-- resources/views/components/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ $title ?? config('app.name') }}</title>
    @livewireStyles
</head>

<body class="antialiased">
    {{ $slot }}
    @filamentScripts
    @livewireScripts
</body>

</html>
