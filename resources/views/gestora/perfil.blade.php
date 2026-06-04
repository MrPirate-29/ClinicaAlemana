<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Mi Perfil — CRM Centro Médico</title>
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
        /* Inputs */
        .field-input {
            width: 100%; border: 1.5px solid #E5E7EB; border-radius: 8px;
            padding: 10px 14px; font-size: 14px; font-family: 'Poppins', sans-serif;
            background: #fff; transition: border-color .2s, box-shadow .2s; outline: none;
        }
        .field-input:focus { border-color: #1A3A6B; box-shadow: 0 0 0 3px rgba(26,58,107,0.1); }
        .field-readonly { background: #F9FAFB; color: #6B7280; cursor: not-allowed; }
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
            <a href="{{ route('gestora.perfil') }}" class="sidebar-link nav-active w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all">
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
                <span class="font-semibold text-[#1A3A6B] text-sm">Mi Perfil</span>
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
        <div class="max-w-3xl mx-auto space-y-6">

            @if(session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-medium">
                <span class="material-symbols-outlined text-green-600 ms-fill">check_circle</span>
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
                <span class="material-symbols-outlined text-red-500 ms-fill">error</span>
                {{ $errors->first() }}
            </div>
            @endif

            {{-- ── Avatar + nombre ─────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
                <div class="w-16 h-16 rounded-full bg-[#1A3A6B] flex items-center justify-center text-white font-bold text-2xl flex-shrink-0">
                    {{ strtoupper(substr($usuario->nom_usu ?? 'G', 0, 1)) }}{{ strtoupper(substr($usuario->apat_usu ?? 'R', 0, 1)) }}
                </div>
                <div>
                    <p class="text-lg font-bold text-[#1A3A6B]">
                        {{ trim(($usuario->nom_usu ?? '') . ' ' . ($usuario->apat_usu ?? '')) ?: 'Gestora' }}
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                        <span class="text-xs text-gray-500 font-medium">Gestora de Consultas · Activa</span>
                    </div>
                </div>
            </div>

            {{-- ── Información Personal ─────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#1A3A6B] text-[20px]">person</span>
                        <h2 class="font-bold text-[#1A3A6B] text-base">Información Personal</h2>
                    </div>
                    <button id="btn-editar" onclick="toggleEdit()"
                            class="flex items-center gap-1.5 text-sm font-semibold text-[#1A3A6B] hover:text-[#0f2a5e] transition-colors">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                        <span id="btn-editar-label">Editar</span>
                    </button>
                </div>

                {{-- Vista de datos --}}
                <div id="perfil-vista" class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Nombres</p>
                            <p class="text-sm font-medium text-gray-800">{{ $usuario->nom_usu ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Apellidos</p>
                            <p class="text-sm font-medium text-gray-800">{{ $usuario->apat_usu ?? '—' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Correo Electrónico</p>
                            <p class="text-sm font-medium text-gray-800">{{ $usuario->coe_usu ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Rol</p>
                            <span class="inline-flex items-center gap-1.5 bg-[#EEF4FF] text-[#1A3A6B] text-xs font-semibold px-3 py-1 rounded-full">
                                <span class="material-symbols-outlined text-[14px] ms-fill">badge</span>
                                Gestora de Turnos
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Formulario edición --}}
                <div id="perfil-form" class="hidden p-6">
                    <form method="POST" action="{{ route('gestora.perfil.update') }}">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">
                                    Nombres <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nom_usu"
                                       value="{{ old('nom_usu', $usuario->nom_usu) }}"
                                       class="field-input" required/>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Apellidos</label>
                                <input type="text" name="apat_usu"
                                       value="{{ old('apat_usu', $usuario->apat_usu) }}"
                                       class="field-input"/>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Correo Electrónico</label>
                                <div class="field-input field-readonly flex items-center gap-2 rounded-lg">
                                    <span class="material-symbols-outlined text-gray-400 text-[18px]">lock</span>
                                    <span class="text-sm">{{ $usuario->coe_usu ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-5 justify-end">
                            <button type="button" onclick="toggleEdit()"
                                    class="px-5 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-semibold hover:border-gray-400 transition-all">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-[#1A3A6B] text-white rounded-lg text-sm font-semibold hover:bg-[#0f2a5e] transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">save</span>
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Seguridad ────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#1A3A6B] text-[20px]">lock</span>
                    <h2 class="font-bold text-[#1A3A6B] text-base">Seguridad</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#EEF4FF] flex items-center justify-center">
                                <span class="material-symbols-outlined text-[#1A3A6B] text-[18px]">password</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Contraseña</p>
                                <p class="text-xs text-gray-400 mt-0.5">••••••••••</p>
                            </div>
                        </div>
                        <button onclick="document.getElementById('pass-modal').style.display='flex'"
                                class="flex items-center gap-1.5 text-sm font-semibold text-[#1A3A6B] hover:text-[#0f2a5e] transition-colors">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                            Cambiar
                        </button>
                    </div>
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

{{-- ════════════ MODAL CAMBIAR CONTRASEÑA ════════════ --}}
<div id="pass-modal" style="display:none" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[200]">
    <div class="bg-white rounded-2xl shadow-2xl p-7 max-w-sm w-full mx-4">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-full bg-[#EEF4FF] flex items-center justify-center">
                <span class="material-symbols-outlined text-[#1A3A6B]">lock_reset</span>
            </div>
            <div>
                <h3 class="font-bold text-[#1A3A6B] text-base">Cambiar contraseña</h3>
                <p class="text-xs text-gray-400">Ingresa tu contraseña actual y la nueva</p>
            </div>
        </div>
        <form method="POST" action="{{ route('gestora.perfil.update') }}" id="form-pass">
            @csrf @method('PUT')
            <input type="hidden" name="cambiar_pass" value="1"/>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Contraseña actual</label>
                    <input type="password" name="password_actual" class="field-input" placeholder="••••••••" required/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Nueva contraseña</label>
                    <input type="password" name="password" class="field-input" placeholder="Mínimo 8 caracteres" required/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="field-input" placeholder="Repite la nueva contraseña" required/>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="document.getElementById('pass-modal').style.display='none'"
                        class="flex-1 py-2.5 border border-gray-300 text-gray-600 rounded-lg font-semibold text-sm hover:border-red-300 hover:text-red-500 transition-all">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-[#1A3A6B] text-white rounded-lg font-semibold text-sm hover:bg-[#0f2a5e] transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Guardar
                </button>
            </div>
        </form>
    </div>
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

function toggleEdit() {
    const vista = document.getElementById('perfil-vista');
    const form  = document.getElementById('perfil-form');
    const lbl   = document.getElementById('btn-editar-label');
    const editing = form.classList.toggle('hidden');
    vista.classList.toggle('hidden', !editing);
    lbl.textContent = editing ? 'Editar' : 'Cancelar';
}
</script>
</body>
</html>
