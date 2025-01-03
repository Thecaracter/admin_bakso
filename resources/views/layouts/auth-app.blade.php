<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Bakso Boled Karawang</title>
    @vite('resources/css/app.css')
</head>

<body class="relative min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50">
    <!-- Animated Background Circles -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div
            class="absolute -top-20 -left-20 w-72 h-72 bg-amber-400/30 rounded-full animate-[bounce_8s_ease-in-out_infinite]">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-96 h-96 bg-orange-400/30 rounded-full animate-[ping_6s_ease-in-out_infinite]">
        </div>
        <div
            class="absolute bottom-1/4 left-1/3 w-80 h-80 bg-yellow-400/30 rounded-full animate-[pulse_4s_ease-in-out_infinite]">
        </div>
        <div
            class="absolute -bottom-20 -right-20 w-64 h-64 bg-red-400/30 rounded-full animate-[bounce_7s_ease-in-out_infinite]">
        </div>
    </div>

    <!-- Content -->
    @yield('content')
</body>

</html>
