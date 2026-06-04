<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Acceso') — CRM Centro Médico Alemana</title>

    {{-- Fuentes CDN — igual que el resto del sistema --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    {{-- CSS estático servido directamente — sin Vite, sin asset(), sin hashes.
         URL relativa /css/login.css funciona en cualquier entorno (local, Render, etc.)
         sin importar HTTP/HTTPS ni configuración de proxies. --}}
    <link rel="stylesheet" href="/css/login.css"/>
</head>
<body>

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')

</body>
</html>
