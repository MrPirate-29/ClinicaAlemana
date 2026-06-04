<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Notificaciones — CRM Centro Médico</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
        .ms-fill { font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
        /* Sidebar */
        #sidebar { width: 240px; transition: width 0.3s ease; }
        #sidebar.collapsed { width: 64px; }
        .sidebar-label { white-space: nowrap; overflow: hidden; transition: opacity 0.15s; flex-shrink: 0; }
        #sidebar.collapsed .sidebar-label { opacity: 0; max-width: 0; pointer-events: none; }
        #sidebar.collapsed .sidebar-badge { display: none; }
        #sidebar.collapsed .sidebar-link { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
        .nav-active { box-shadow: inset 3px 0 0 #F5A623; background: rgba(255,255,255,0.1); color: #F5A623 !important; }
        .nav-item { position: relative; }
        .nav-item .tooltip {
            position: absolute; left: 68px; top: 50%; transform: translateY(-50%);
            background: #0f2a5e; color: #fff; padding: 4px 10px;
            border-radius: 6px; font-size: 12px; white-space: nowrap;
            opacity: 0; pointer-events: none; transition: opacity 0.15s; z-index: 100;
        }
        #sidebar.collapsed .nav-item:hover .tooltip { opacity: 1; }
        #main-wrapper { margin-left: 240px; transition: margin-left 0.3s ease; }
        #main-wrapper.sidebar-collapsed { margin-left: 64px; }
        /* Logo toggle */
        .sidebar-logo { display: block; flex-shrink: 0; max-height: 36px; width: auto; }
        #sidebar.collapsed .sidebar-logo { display: none; }
        .sidebar-icon { display: none !important; }
        #sidebar.collapsed .sidebar-icon { display: flex !important; align-items: center; }
        /* Notif card */
        .notif-card {
            display: flex; gap: 14px; padding: 16px 20px;
            background: #fff; border: 1px solid #E5E7EB; border-radius: 12px;
            transition: box-shadow .15s;
        }
        .notif-card:hover { box-shadow: 0 4px 16px rgba(26,58,107,0.08); }
        .notif-card--nueva { border-left: 3px solid #F5A623; background: #FFFDF5; }
        .notif-icon {
            width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .notif-icon--cita    { background: #EEF4FF; color: #1A3A6B; }
        .notif-icon--sistema { background: #F0FDF4; color: #15803D; }
        .notif-icon--alerta  { background: #FEF3C7; color: #B45309; }
        @keyframes bell { 0%,100%{transform:rotate(0)} 25%{transform:rotate(12deg)} 75%{transform:rotate(-12deg)} }
        .bell-anim { animation: bell 2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-[#F7F8FA] min-h-screen flex overflow-hidden">

{{-- ════════════ SIDEBAR ════════════ --}}
<aside id="sidebar" class="fixed left-0 top-0 h-screen bg-[#1A3A6B] flex flex-col z-50">
    <div class="flex items-center justify-between px-3 py-3 border-b border-white/10 min-h-[64px]">
        <div class="flex items-center gap-2 overflow-hidden">
            <img src="{{ asset('img/logo-alemana.jpg') }}" alt="Clínica Alemana" class="sidebar-logo rounded bg-white px-1.5 py-0.5">
            <span class="material-symbols-outlined text-white text-2xl ms-fill sidebar-icon">add_box</span>
        </div>
        <button onclick="toggleSidebar()" class="w-8 h-8 flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 rounded-lg transition-all flex-shrink-0 ml-1">
            <span class="material-symbols-outlined text-[20px]" id="toggle-icon">menu_open</span>
        </button>
    </div>
    <nav class="flex flex-col gap-0.5 px-2 py-3 flex-1 overflow-y-auto overflow-x-hidden">
        <div class="nav-item w-full">
            <a href="{{ route('gestora.panel') }}" class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
                <span class="material-symbols-outlined flex-shrink-0">home</span>
                <span class="sidebar-label">Inicio</span>
            </a>
            <span class="tooltip">Inicio</span>
        </div>
        <div class="nav-item w-full">
            <a href="{{ route('gestora.perfil') }}" class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
                <span class="material-symbols-outlined flex-shrink-0">manage_accounts</span>
                <span class="sidebar-label">Mi Perfil</span>
            </a>
            <span class="tooltip">Mi Perfil</span>
        </div>
        <div class="my-1 mx-2 border-t border-white/10"></div>
        <div class="nav-item w-full">
            <a href="{{ route('gestora.consultas.nueva') }}" class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
                <span class="material-symbols-outlined flex-shrink-0">calendar_today</span>
                <span class="sidebar-label">Nueva Consulta</span>
            </a>
            <span class="tooltip">Nueva Consulta</span>
        </div>
        <div class="nav-item w-full">
            <a href="{{ route('gestora.consultas.historial') }}" class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
                <span class="material-symbols-outlined flex-shrink-0">list_alt</span>
                <span class="sidebar-label">Historial de Citas</span>
            </a>
            <span class="tooltip">Historial de Citas</span>
        </div>
        <div class="nav-item w-full">
            <a href="{{ route('gestora.pacientes') }}" class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
                <span class="material-symbols-outlined flex-shrink-0">group</span>
                <span class="sidebar-label">Pacientes</span>
            </a>
            <span class="tooltip">Pacientes</span>
        </div>
    </nav>
    <div class="border-t border-white/10 px-2 py-2 flex flex-col gap-0.5">
        <div class="nav-item w-full">
            <a href="{{ route('gestora.notificaciones') }}" class="sidebar-link nav-active w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all">
                <span class="material-symbols-outlined flex-shrink-0">notifications</span>
                <span class="sidebar-label">Notificaciones</span>
                <span class="ml-auto bg-[#F5A623] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full sidebar-badge leading-none">3</span>
            </a>
            <span class="tooltip">Notificaciones</span>
        </div>
        <div class="nav-item w-full">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-red-400 hover:text-red-300 hover:bg-red-500/10">
                    <span class="material-symbols-outlined flex-shrink-0">logout</span>
                    <span class="sidebar-label">Cerrar Sesión</span>
                </button>
            </form>
            <span class="tooltip">Cerrar Sesión</span>
        </div>
    </div>
</aside>

{{-- ════════════ MAIN ════════════ --}}
<div id="main-wrapper" class="flex-1 flex flex-col min-h-screen">

    {{-- HEADER --}}
    <header class="h-[60px] bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-3">
            <nav class="flex items-center text-sm">
                <span class="text-gray-400">Gestora</span>
                <span class="material-symbols-outlined text-[16px] mx-1 text-gray-300">chevron_right</span>
                <span class="font-semibold text-[#1A3A6B] text-sm">Notificaciones</span>
            </nav>
            <div class="h-4 w-px bg-gray-200 mx-1"></div>
            <div class="flex items-center gap-1 bg-[#EEF4FF] px-3 py-1 rounded-full">
                <span class="material-symbols-outlined text-[#1A3A6B] text-[16px] ms-fill">location_on</span>
                <span class="text-xs font-semibold text-[#1A3A6B]">Centro Médico — Achumani</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Gestora de Turnos</p>
                <p class="text-sm font-semibold text-[#1A3A6B]">{{ Auth::user()->nom_usu ?? 'Gestora' }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-[#1A3A6B] flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr(Auth::user()->nom_usu ?? 'G', 0, 1)) }}{{ strtoupper(substr(Auth::user()->apat_usu ?? 'R', 0, 1)) }}
            </div>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="flex-1 p-6 overflow-y-auto">
        <div class="max-w-2xl mx-auto">

            {{-- Cabecera --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl font-bold text-[#1A3A6B]">Notificaciones</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Avisos y alertas del sistema</p>
                </div>
            </div>

            {{-- Estado vacío --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-16 px-8 text-center">
                <div class="relative w-20 h-20 mx-auto mb-6">
                    <div class="absolute inset-0 bg-[#EEF4FF] rounded-full"></div>
                    <div class="relative flex items-center justify-center h-full">
                        <span class="material-symbols-outlined text-[#1A3A6B] text-4xl ms-fill bell-anim">notifications</span>
                    </div>
                </div>
                <h2 class="text-lg font-bold text-[#1A3A6B] mb-2">Sin notificaciones por ahora</h2>
                <p class="text-sm text-gray-400 max-w-sm mx-auto leading-relaxed">
                    Aquí aparecerán avisos sobre citas próximas, cambios de estado y
                    eventos importantes del sistema en tiempo real.
                </p>
                <div class="flex gap-3 justify-center mt-6">
                    <a href="{{ route('gestora.consultas.nueva') }}"
                       class="flex items-center gap-2 bg-[#1A3A6B] text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-[#0f2a5e] transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Nueva Consulta
                    </a>
                    <a href="{{ route('gestora.consultas.historial') }}"
                       class="flex items-center gap-2 border border-gray-300 text-gray-600 px-5 py-2.5 rounded-lg text-sm font-semibold hover:border-[#1A3A6B] hover:text-[#1A3A6B] transition-all">
                        <span class="material-symbols-outlined text-[18px]">list_alt</span>
                        Ver Historial
                    </a>
                </div>
            </div>

            {{-- Tipos de notificaciones que llegaran --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-[#EEF4FF] rounded-xl p-4 flex flex-col gap-2">
                    <div class="notif-icon notif-icon--cita w-9 h-9">
                        <span class="material-symbols-outlined text-[18px] ms-fill">event_available</span>
                    </div>
                    <p class="text-xs font-bold text-[#1A3A6B]">Citas</p>
                    <p class="text-[11px] text-[#1A3A6B]/60 leading-relaxed">Confirmaciones y recordatorios de citas agendadas.</p>
                </div>
                <div class="bg-amber-50 rounded-xl p-4 flex flex-col gap-2">
                    <div class="notif-icon notif-icon--alerta w-9 h-9">
                        <span class="material-symbols-outlined text-[18px] ms-fill">warning</span>
                    </div>
                    <p class="text-xs font-bold text-amber-700">Alertas</p>
                    <p class="text-[11px] text-amber-600/70 leading-relaxed">Slots ocupados, cancelaciones y cambios urgentes.</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 flex flex-col gap-2">
                    <div class="notif-icon notif-icon--sistema w-9 h-9">
                        <span class="material-symbols-outlined text-[18px] ms-fill">info</span>
                    </div>
                    <p class="text-xs font-bold text-green-700">Sistema</p>
                    <p class="text-[11px] text-green-600/70 leading-relaxed">Actualizaciones, mantenimiento y eventos del CRM.</p>
                </div>
            </div>

        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-[#014A8F] text-white py-3 px-6 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <span class="font-bold text-sm">CRM Centro Médico</span>
            <span class="text-[10px] opacity-50 ml-1">v1.0</span>
        </div>
        <p class="text-xs opacity-60">© 2025 Alemana Red de Salud. Todos los derechos reservados.</p>
        <div class="flex gap-4">
            <a href="#" class="text-xs opacity-60 hover:opacity-100">Privacidad</a>
            <a href="#" class="text-xs opacity-60 hover:opacity-100">Soporte</a>
        </div>
    </footer>
</div>

<script>
function toggleSidebar() {
    const sidebar   = document.getElementById('sidebar');
    const main      = document.getElementById('main-wrapper');
    const icon      = document.getElementById('toggle-icon');
    const collapsed = sidebar.classList.toggle('collapsed');
    main.classList.toggle('sidebar-collapsed', collapsed);
    icon.textContent = collapsed ? 'menu' : 'menu_open';
    localStorage.setItem('sidebar_collapsed', collapsed ? '1' : '0');
}
(function () {
    if (localStorage.getItem('sidebar_collapsed') === '1') {
        document.getElementById('sidebar').classList.add('collapsed');
        document.getElementById('main-wrapper').classList.add('sidebar-collapsed');
        const icon = document.getElementById('toggle-icon');
        if (icon) icon.textContent = 'menu';
    }
})();
</script>
</body>
</html>
