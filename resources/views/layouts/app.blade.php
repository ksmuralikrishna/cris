<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mall Registration')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981', // emerald-500
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800 antialiased font-sans flex flex-col min-h-screen">
    <main class="flex-grow flex items-center justify-center p-4">
        @yield('content')
    </main>
</body>
</html>



