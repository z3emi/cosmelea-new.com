<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Cosmelea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Cairo', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="text-4xl font-bold text-primary">كوزميليا | Cosmelea</a>
        </div>
        <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
             @yield('content')
        </div>
    </div>
</body>
</html>