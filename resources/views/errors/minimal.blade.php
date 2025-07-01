<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Error')</title>

    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Configuración personalizada de Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        principal: '#39a900',
                        fondoSuave: '#f3f4f6', // gris suave (tailwind gray-100)
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }
    </style>
</head>
<body class="bg-fondoSuave min-h-screen flex flex-col items-center justify-center px-4 py-8">

    {{-- Logo --}}
    <div class="mb-6">
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/404.png') }}" alt="Logo SSET" class="w-24 h-24 mx-auto">
        </a>
    </div>

    {{-- Contenido principal --}}
    <main class="max-w-xl text-center bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-center space-x-4">
            <span class="text-4xl font-bold text-principal">@yield('code')</span>
            <h1 class="text-xl font-semibold text-principal uppercase">@yield('message')</h1>
        </div>

        @hasSection('description')
            <p class="mt-4 text-gray-600">@yield('description')</p>
        @endif

        @hasSection('suggestion')
            <p class="mt-2 text-gray-500">@yield('suggestion')</p>
        @endif

        @hasSection('image')
            <div class="mt-6">
                @yield('image')
            </div>
        @endif

        <a href="{{ url('/') }}" class="mt-8 inline-block bg-principal text-white px-6 py-2 rounded shadow hover:bg-green-700 transition">
            Volver al inicio
        </a>
    </main>

</body>
</html>
