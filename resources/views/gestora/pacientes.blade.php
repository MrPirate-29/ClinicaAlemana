<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Pacientes — CRM Centro Médico</title>
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
        /* Filtros / tabla */
        .field-input {
            border: 1.5px solid #E5E7EB; border-radius: 8px; padding: 8px 12px;
            font-size: 13px; font-family: 'Poppins', sans-serif; outline: none;
            transition: border-color .2s, box-shadow .2s; background: #fff;
        }
        .field-input:focus { border-color: #1A3A6B; box-shadow: 0 0 0 3px rgba(26,58,107,0.1); }
        tbody tr:hover { background: #FAFCFF; }
        .page-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 6px; font-size: 13px; font-weight: 500; transition: all .15s;
        }
        .page-btn.active { background: #1A3A6B; color: #fff; }
        .page-btn:not(.active):hover { background: #EEF4FF; color: #1A3A6B; }
        .page-btn.disabled { opacity: 0.35; cursor: not-allowed; }
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
            <a href="{{ route('gestora.pacientes') }}" class="sidebar-link nav-active w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all">
                <span class="material-symbols-outlined flex-shrink-0 ms-fill">group</span>
                <span class="sidebar-label">Pacientes</span>
            </a>
            <span class="tooltip">Pacientes</span>
        </div>
    </nav>
    <div class="border-t border-white/10 px-2 py-2 flex flex-col gap-0.5">
        <div class="nav-item w-full">
            <a href="{{ route('gestora.notificaciones') }}" class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
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
                <span class="font-semibold text-[#1A3A6B] text-sm">Pacientes</span>
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
        <div class="max-w-7xl mx-auto space-y-5">

            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#EEF4FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#1A3A6B] text-[20px] ms-fill">group</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[#1A3A6B]">{{ $total }}</p>
                        <p class="text-xs text-gray-400">Total pacientes</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-amber-600 text-[20px] ms-fill">payments</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-amber-700">{{ $totalPrivados }}</p>
                        <p class="text-xs text-gray-400">Consulta privada</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-green-600 text-[20px] ms-fill">person_add</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-700">{{ $totalMes }}</p>
                        <p class="text-xs text-gray-400">Registrados este mes</p>
                    </div>
                </div>
            </div>

            {{-- Panel tabla --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Header panel --}}
                <div class="p-5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#1A3A6B] ms-fill">group</span>
                        <h2 class="font-bold text-[#1A3A6B] text-base">Lista de Pacientes</h2>
                        <span class="bg-[#EEF4FF] text-[#1A3A6B] text-xs font-bold px-2.5 py-1 rounded-full ml-1">
                            {{ $pacientes->total() }} registros
                        </span>
                    </div>
                    <a href="{{ route('gestora.consultas.nueva') }}"
                       class="flex items-center gap-2 bg-[#1A3A6B] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#0f2a5e] transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Nueva Consulta
                    </a>
                </div>

                {{-- Filtros --}}
                <form method="GET" action="{{ route('gestora.pacientes') }}"
                      class="p-4 bg-gray-50 border-b border-gray-100 flex flex-wrap gap-3 items-end">

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Buscar</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                            <input type="text" name="q" value="{{ request('q') }}"
                                   class="field-input w-full pl-9"
                                   placeholder="Nombre, apellido o CI..."/>
                        </div>
                    </div>

                    <div class="min-w-[180px]">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Seguro</label>
                        <div class="relative">
                            <select name="seguro" class="field-input w-full pr-8 appearance-none">
                                <option value="">Todos los seguros</option>
                                <option value="Particular"               {{ request('seguro') === 'Particular'              ? 'selected' : '' }}>Particular</option>
                                <option value="Alianza_Seguros"          {{ request('seguro') === 'Alianza_Seguros'          ? 'selected' : '' }}>Alianza Seguros</option>
                                <option value="Bisa_Seguros"             {{ request('seguro') === 'Bisa_Seguros'             ? 'selected' : '' }}>Bisa Seguros</option>
                                <option value="Univida"                  {{ request('seguro') === 'Univida'                  ? 'selected' : '' }}>Univida</option>
                                <option value="Nacional_Seguros"         {{ request('seguro') === 'Nacional_Seguros'         ? 'selected' : '' }}>Nacional Seguros</option>
                                <option value="Seguros_Internacionales"  {{ request('seguro') === 'Seguros_Internacionales'  ? 'selected' : '' }}>Seguros Internacionales</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400 text-[18px]">expand_more</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex items-center gap-1 bg-[#1A3A6B] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#0f2a5e] transition-all">
                            <span class="material-symbols-outlined text-[16px]">filter_list</span>
                            Filtrar
                        </button>
                        @if(request()->hasAny(['q','seguro']))
                        <a href="{{ route('gestora.pacientes') }}"
                           class="flex items-center gap-1 border border-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-semibold hover:border-red-300 hover:text-red-500 transition-all">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                            Limpiar
                        </a>
                        @endif
                    </div>
                </form>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wide">Paciente</th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wide">CI</th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wide">Teléfono</th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wide">Seguro</th>
                                <th class="text-center py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wide">Tipo</th>
                                <th class="text-center py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wide">Citas</th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wide">Registro</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($pacientes as $pac)
                            @php
                                $seguroLabel = match($pac->seguro_pac) {
                                    'Particular'              => 'Particular',
                                    'Alianza_Seguros'         => 'Alianza',
                                    'Bisa_Seguros'            => 'Bisa',
                                    'Univida'                 => 'Univida',
                                    'Nacional_Seguros'        => 'Nacional',
                                    'Seguros_Internacionales' => 'Internacional',
                                    default                   => $pac->seguro_pac ?? '—',
                                };
                            @endphp
                            <tr class="transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-[#EEF4FF] flex items-center justify-center text-[#1A3A6B] font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($pac->nom_pac ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 leading-tight text-sm">
                                                {{ trim(($pac->nom_pac ?? '') . ' ' . ($pac->apat_pac ?? '')) ?: '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-sm text-gray-600 font-mono">{{ $pac->ci_pac ?? '—' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-sm text-gray-500">{{ $pac->tel_pac ?? '—' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-block bg-[#EEF4FF] text-[#1A3A6B] text-xs font-semibold px-2 py-0.5 rounded">
                                        {{ $seguroLabel }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($pac->priv_pac)
                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded">
                                            <span class="material-symbols-outlined text-[12px] ms-fill">payments</span>
                                            Privada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-0.5 rounded">
                                            <span class="material-symbols-outlined text-[12px] ms-fill">shield</span>
                                            Seguro
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($pac->citas_count > 0)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-[#1A3A6B] text-white text-xs font-bold">
                                            {{ $pac->citas_count }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($pac->fecreg_pac)
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pac->fecreg_pac)->format('d/m/Y') }}</p>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-gray-300 text-5xl block mb-3">person_off</span>
                                    <p class="text-gray-500 font-semibold">No se encontraron pacientes</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        @if(request()->hasAny(['q','seguro']))
                                            Prueba con otros filtros o
                                            <a href="{{ route('gestora.pacientes') }}" class="text-[#1A3A6B] underline">limpiar la búsqueda</a>
                                        @else
                                            Los pacientes aparecerán aquí al registrar consultas.
                                        @endif
                                    </p>
                                    <a href="{{ route('gestora.consultas.nueva') }}"
                                       class="inline-flex items-center gap-2 mt-4 bg-[#1A3A6B] text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-[#0f2a5e] transition-all">
                                        <span class="material-symbols-outlined text-[18px]">add</span>
                                        Registrar primera consulta
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($pacientes->hasPages())
                <div class="p-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                    <p class="text-xs text-gray-500">
                        Mostrando <strong>{{ $pacientes->firstItem() }}</strong>–<strong>{{ $pacientes->lastItem() }}</strong>
                        de <strong>{{ $pacientes->total() }}</strong> pacientes
                    </p>
                    <div class="flex items-center gap-1">
                        @if($pacientes->onFirstPage())
                            <span class="page-btn disabled text-gray-400"><span class="material-symbols-outlined text-[18px]">chevron_left</span></span>
                        @else
                            <a href="{{ $pacientes->previousPageUrl() }}" class="page-btn text-gray-500"><span class="material-symbols-outlined text-[18px]">chevron_left</span></a>
                        @endif
                        @foreach($pacientes->getUrlRange(max(1,$pacientes->currentPage()-2), min($pacientes->lastPage(),$pacientes->currentPage()+2)) as $page => $url)
                            <a href="{{ $url }}" class="page-btn {{ $page == $pacientes->currentPage() ? 'active' : 'text-gray-500' }}">{{ $page }}</a>
                        @endforeach
                        @if($pacientes->hasMorePages())
                            <a href="{{ $pacientes->nextPageUrl() }}" class="page-btn text-gray-500"><span class="material-symbols-outlined text-[18px]">chevron_right</span></a>
                        @else
                            <span class="page-btn disabled text-gray-400"><span class="material-symbols-outlined text-[18px]">chevron_right</span></span>
                        @endif
                    </div>
                </div>
                @endif

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
