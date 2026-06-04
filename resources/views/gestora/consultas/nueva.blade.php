<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Nueva Consulta — CRM Centro Médico</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        /* Input focus */
        .field-input {
            width: 100%; border: 1.5px solid #E5E7EB; border-radius: 8px;
            padding: 10px 14px; font-size: 14px; font-family: 'Poppins', sans-serif;
            background: #fff; transition: border-color .2s, box-shadow .2s; outline: none;
        }
        .field-input:focus {
            border-color: #1A3A6B;
            box-shadow: 0 0 0 3px rgba(26,58,107,0.1);
        }
        .field-input.error { border-color: #EF4444; }
        /* Chip slot */
        .slot-chip {
            display: inline-flex; align-items: center;
            padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 500;
            border: 1.5px solid #C7D9F5; background: #F0F4FF; color: #1A3A6B;
            cursor: pointer; transition: all .15s;
        }
        .slot-chip:hover:not(.occupied) { background: #dbeafe; }
        .slot-chip.selected { background: #1A3A6B; color: #fff; border-color: #1A3A6B; }
        .slot-chip.occupied { background: #F3F4F6; color: #9CA3AF; border-color: #E5E7EB; cursor: not-allowed; }
        /* Doctor card */
        .doctor-card {
            border: 1.5px solid #E5E7EB; border-radius: 12px; padding: 20px;
            cursor: pointer; transition: all .2s; background: #fff; position: relative;
        }
        .doctor-card:hover { border-color: #1A3A6B; box-shadow: 0 4px 16px rgba(26,58,107,0.12); }
        .doctor-card.selected {
            border: 2px solid #1A3A6B; box-shadow: 0 4px 20px rgba(26,58,107,0.18);
            background: #FAFCFF;
        }
        .selected-badge {
            position: absolute; top: 0; right: 0;
            background: #1A3A6B; color: #fff; font-size: 11px; font-weight: 600;
            padding: 4px 10px; border-radius: 0 12px 0 8px;
        }
        /* Toggle switch */
        .toggle-track {
            width: 44px; height: 24px; background: #D1D5DB; border-radius: 9999px;
            position: relative; cursor: pointer; transition: background .2s;
        }
        .toggle-track.on { background: #1A3A6B; }
        .toggle-thumb {
            position: absolute; top: 2px; left: 2px; width: 20px; height: 20px;
            background: #fff; border-radius: 50%; transition: transform .2s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }
        .toggle-track.on .toggle-thumb { transform: translateX(20px); }
        /* Steps */
        .step-circle {
            width: 40px; height: 40px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; font-weight: 700;
            font-size: 15px; transition: all .3s; flex-shrink: 0;
        }
        .step-circle.active  { background: #1A3A6B; color: #fff; box-shadow: 0 0 0 4px rgba(26,58,107,0.2); }
        .step-circle.done    { background: #16a34a; color: #fff; }
        .step-circle.pending { background: #fff; border: 2px solid #D1D5DB; color: #9CA3AF; }
        .step-line { flex: 1; height: 2px; margin: 0 8px; }
        .step-line.done    { background: #16a34a; }
        .step-line.pending { background: #E5E7EB; }
        /* Specialty dropdown */
        #esp-panel {
            position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 50;
            background: #fff; border: 1.5px solid #E5E7EB; border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12); display: none; max-height: 260px;
        }
        #esp-panel.open { display: block; }
        #esp-list { overflow-y: auto; max-height: 210px; }
        .esp-option {
            padding: 9px 14px; font-size: 13px; cursor: pointer; display: flex;
            align-items: center; gap: 8px;
        }
        .esp-option:hover { background: #F0F4FF; }
        /* Confirm btn */
        #btn-confirm {
            width: 100%; padding: 14px; background: #1A3A6B; color: #fff;
            border: none; border-radius: 8px; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: all .2s; display: flex; align-items: center;
            justify-content: center; gap: 8px; font-family: 'Poppins', sans-serif;
        }
        #btn-confirm:disabled {
            background: #E5E7EB; color: #9CA3AF; cursor: not-allowed;
        }
        #btn-confirm:not(:disabled):hover { background: #0f2a5e; transform: scale(1.01); }
        /* Overlay modal */
        #confirm-modal {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            display: flex; align-items: center; justify-content: center; z-index: 200;
        }
        /* Success */
        @keyframes checkScale { from{transform:scale(0)} to{transform:scale(1)} }
        .check-anim { animation: checkScale .4s ease-out forwards; }
    </style>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: {
                    'primary': '#1A3A6B', 'primary-dark': '#0f2a5e',
                    'gold':    '#F5A623', 'bg-app': '#F7F8FA',
                },
            }},
        }
    </script>
</head>
<body class="bg-[#F7F8FA] min-h-screen flex overflow-hidden">

{{-- ════════════ SIDEBAR ════════════ --}}
<aside id="sidebar" class="fixed left-0 top-0 h-screen bg-[#1A3A6B] flex flex-col z-50">

    {{-- Logo + toggle --}}
    <div class="flex items-center justify-between px-3 py-3 border-b border-white/10 min-h-[64px]">
        <div class="flex items-center gap-2 overflow-hidden">
            <img src="{{ asset('img/logo-alemana.jpg') }}" alt="Clínica Alemana" class="sidebar-logo rounded bg-white px-1.5 py-0.5">
            <span class="material-symbols-outlined text-white text-2xl ms-fill sidebar-icon">add_box</span>
        </div>
        <button onclick="toggleSidebar()"
                class="w-8 h-8 flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 rounded-lg transition-all flex-shrink-0 ml-1">
            <span class="material-symbols-outlined text-[20px]" id="toggle-icon">menu_open</span>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex flex-col gap-0.5 px-2 py-3 flex-1 overflow-y-auto overflow-x-hidden">

        {{-- Inicio --}}
        <div class="nav-item w-full">
            <a href="{{ route('gestora.panel') }}"
               class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
                <span class="material-symbols-outlined flex-shrink-0">home</span>
                <span class="sidebar-label">Inicio</span>
            </a>
            <span class="tooltip">Inicio</span>
        </div>

        {{-- Perfil del Gestor --}}
        <div class="nav-item w-full">
            <a href="{{ route('gestora.perfil') }}"
               class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
                <span class="material-symbols-outlined flex-shrink-0">manage_accounts</span>
                <span class="sidebar-label">Mi Perfil</span>
            </a>
            <span class="tooltip">Mi Perfil</span>
        </div>

        <div class="my-1 mx-2 border-t border-white/10"></div>

        {{-- Nueva Consulta (activo) --}}
        <div class="nav-item w-full">
            <a href="{{ route('gestora.consultas.nueva') }}"
               class="sidebar-link nav-active w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all">
                <span class="material-symbols-outlined ms-fill flex-shrink-0">calendar_today</span>
                <span class="sidebar-label">Nueva Consulta</span>
            </a>
            <span class="tooltip">Nueva Consulta</span>
        </div>

        {{-- Historial --}}
        <div class="nav-item w-full">
            <a href="{{ route('gestora.consultas.historial') }}"
               class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
                <span class="material-symbols-outlined flex-shrink-0">list_alt</span>
                <span class="sidebar-label">Historial de Citas</span>
            </a>
            <span class="tooltip">Historial de Citas</span>
        </div>

        {{-- Pacientes --}}
        <div class="nav-item w-full">
            <a href="{{ route('gestora.pacientes') }}"
               class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
                <span class="material-symbols-outlined flex-shrink-0">group</span>
                <span class="sidebar-label">Pacientes</span>
            </a>
            <span class="tooltip">Pacientes</span>
        </div>

    </nav>

    {{-- Footer: Notificaciones + Logout --}}
    <div class="border-t border-white/10 px-2 py-2 flex flex-col gap-0.5">

        {{-- Notificaciones --}}
        <div class="nav-item w-full">
            <a href="{{ route('gestora.notificaciones') }}"
               class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-blue-200/70 hover:text-white hover:bg-white/10">
                <span class="material-symbols-outlined flex-shrink-0">notifications</span>
                <span class="sidebar-label">Notificaciones</span>
                <span class="ml-auto bg-[#F5A623] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full sidebar-badge leading-none">3</span>
            </a>
            <span class="tooltip">Notificaciones</span>
        </div>

        {{-- Cerrar Sesión --}}
        <div class="nav-item w-full">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="sidebar-link w-full h-11 flex items-center gap-3 px-3 rounded-lg transition-all text-red-400 hover:text-red-300 hover:bg-red-500/10">
                    <span class="material-symbols-outlined flex-shrink-0">logout</span>
                    <span class="sidebar-label">Cerrar Sesión</span>
                </button>
            </form>
            <span class="tooltip">Cerrar Sesión</span>
        </div>

    </div>
</aside>

{{-- ════════════ MAIN WRAPPER ════════════ --}}
<div id="main-wrapper" class="flex-1 flex flex-col min-h-screen">

    {{-- HEADER --}}
    <header class="h-[60px] bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-3">
            <nav class="flex items-center text-sm">
                <span class="text-gray-400">Consultas</span>
                <span class="material-symbols-outlined text-[16px] mx-1 text-gray-300">chevron_right</span>
                <span class="font-semibold text-[#1A3A6B] text-sm">Nueva Consulta</span>
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
                <p class="text-sm font-semibold text-[#1A3A6B]">
                    Buenos días, {{ Auth::user()->nom_usu ?? 'Gestora' }}
                </p>
            </div>
            <button class="relative">
                <span class="material-symbols-outlined text-[#1A3A6B]">notifications</span>
                <span class="absolute top-0 right-0 w-2 h-2 bg-[#F5A623] rounded-full border-2 border-white"></span>
            </button>
            <div class="w-9 h-9 rounded-full bg-[#1A3A6B] flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr(Auth::user()->nom_usu ?? 'G', 0, 1)) }}{{ strtoupper(substr(Auth::user()->apat_usu ?? 'R', 0, 1)) }}
            </div>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="flex-1 p-6 overflow-y-auto">
        <div class="max-w-4xl mx-auto">

            {{-- ── STEPPER ─────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6 flex items-center">
                {{-- Step 1 --}}
                <div class="flex flex-col items-center min-w-[80px]" id="step-ui-1">
                    <div class="step-circle active" id="circle-1">
                        <span class="material-symbols-outlined text-[20px]">person</span>
                    </div>
                    <span class="text-xs font-semibold text-[#1A3A6B] mt-1 text-center">Datos del Paciente</span>
                </div>
                <div class="step-line pending flex-1" id="line-1"></div>
                {{-- Step 2 --}}
                <div class="flex flex-col items-center min-w-[80px]" id="step-ui-2">
                    <div class="step-circle pending" id="circle-2">
                        <span class="material-symbols-outlined text-[20px]">stethoscope</span>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 mt-1 text-center" id="label-2">Motivo</span>
                </div>
                <div class="step-line pending flex-1" id="line-2"></div>
                {{-- Step 3 --}}
                <div class="flex flex-col items-center min-w-[80px]" id="step-ui-3">
                    <div class="step-circle pending" id="circle-3">
                        <span class="material-symbols-outlined text-[20px]">medical_services</span>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 mt-1 text-center" id="label-3">Médico</span>
                </div>
            </div>

            {{-- ══════════════════════ PASO 1 ══════════════════════ --}}
            <section id="step-1" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-7">
                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-full bg-[#EEF4FF] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[#1A3A6B] text-[20px] ms-fill">assignment_ind</span>
                        </div>
                        <div>
                            <h2 class="font-bold text-[#1A3A6B] text-base tracking-wide">— INFORMACIÓN DEL PACIENTE —</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Complete los campos obligatorios para continuar con la agenda.</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 mb-6"></div>

                    {{-- Grid de campos --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">
                                Nombres <span class="text-red-500">*</span>
                            </label>
                            <input id="nom_pac" type="text" placeholder="Ej. María Paola"
                                   class="field-input" required/>
                            <p class="text-xs text-red-500 mt-1 hidden" id="err-nom_pac"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Apellidos</label>
                            <input id="apat_pac" type="text" placeholder="Ej. Valda Rodríguez"
                                   class="field-input"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">
                                CI / Documento <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">badge</span>
                                <input id="ci_pac" type="text" placeholder="Número de documento"
                                       class="field-input pl-9" required/>
                            </div>
                            <p class="text-xs text-red-500 mt-1 hidden" id="err-ci_pac"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Teléfono de Contacto</label>
                            <div class="flex gap-2">
                                <span class="flex items-center justify-center bg-gray-50 rounded-lg px-3 text-sm font-semibold text-gray-500 border border-gray-200 whitespace-nowrap">+591</span>
                                <input id="tel_pac" type="tel" placeholder="70000000"
                                       class="field-input flex-1"/>
                            </div>
                        </div>
                    </div>

                    {{-- Seguro --}}
                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">
                            Seguro Médico / Convenio <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">shield</span>
                            <select id="seguro_pac" class="field-input pl-9 pr-9 appearance-none" required>
                                <option value="">Seleccione una entidad...</option>
                                <option value="Particular">Particular</option>
                                <option value="Alianza_Seguros">Alianza Seguros S.A.</option>
                                <option value="Bisa_Seguros">Bisa Seguros y Reaseguros S.A.</option>
                                <option value="Univida">Univida</option>
                                <option value="Nacional_Seguros">Nacional Seguros Vida y Salud S.A.</option>
                                <option value="Seguros_Internacionales">Seguros Internacionales</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">expand_more</span>
                        </div>
                        <p class="text-xs text-red-500 mt-1 hidden" id="err-seguro_pac"></p>
                    </div>

                    {{-- Toggle consulta privada --}}
                    <div class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-between transition-all duration-200" id="toggle-row">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[#1A3A6B]">payments</span>
                            <div>
                                <p class="text-sm font-semibold text-[#1A3A6B]">¿Es consulta privada?</p>
                                <p class="text-xs text-gray-400">Activar si el paciente asume el costo total fuera de convenio.</p>
                            </div>
                        </div>
                        <div class="toggle-track" id="toggle-track" onclick="togglePrivada()">
                            <div class="toggle-thumb"></div>
                        </div>
                        <input type="hidden" id="priv_pac" value="0"/>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-7 py-4 flex justify-between items-center border-t border-gray-100">
                    <button onclick="cancelar()" class="px-5 py-2 text-gray-500 font-semibold hover:text-[#1A3A6B] transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">close</span> Cancelar
                    </button>
                    <button id="btn-paso1" onclick="submitPaso1()" disabled
                            class="px-7 py-2.5 bg-gray-200 text-gray-400 rounded-lg font-bold flex items-center gap-2 cursor-not-allowed transition-all text-sm">
                        Continuar <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </section>

            {{-- ══════════════════════ PASO 2 ══════════════════════ --}}
            <section id="step-2" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-7">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-full bg-[#EEF4FF] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[#1A3A6B] text-[20px] ms-fill">stethoscope</span>
                        </div>
                        <div>
                            <h2 class="font-bold text-[#1A3A6B] text-base tracking-wide">— MOTIVO DE CONSULTA —</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Selecciona la especialidad y describe el motivo de la visita.</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 mb-6"></div>

                    {{-- Especialidad con buscador --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">
                            Especialidad <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div id="esp-trigger"
                                 class="field-input flex items-center justify-between cursor-pointer select-none"
                                 onclick="toggleEspPanel()">
                                <span id="esp-label" class="text-gray-400">Seleccionar especialidad...</span>
                                <span class="material-symbols-outlined text-gray-400 text-[20px]">expand_more</span>
                            </div>
                            <input type="hidden" id="id_esp"/>
                            <div id="esp-panel">
                                <div class="p-2 border-b border-gray-100">
                                    <input type="text" id="esp-search"
                                           class="w-full text-sm px-3 py-1.5 border border-gray-200 rounded-lg outline-none focus:border-[#1A3A6B]"
                                           placeholder="Buscar especialidad..."
                                           oninput="filterEsp(this.value)"/>
                                </div>
                                <ul id="esp-list">
                                    @foreach($especialidades as $esp)
                                    <li class="esp-option" data-id="{{ $esp->id_esp }}" data-name="{{ $esp->nom_esp }}"
                                        onclick="selectEsp({{ $esp->id_esp }}, '{{ addslashes($esp->nom_esp) }}')">
                                        <span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span>
                                        {{ $esp->nom_esp }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <p class="text-xs text-red-500 mt-1 hidden" id="err-id_esp"></p>
                    </div>

                    {{-- Tipo de consulta --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">
                            Tipo de Consulta <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="tipo_cit" class="field-input pr-9 appearance-none" onchange="checkPaso2()">
                                <option value="Consulta" selected>🩺 Consulta Regular — Primera consulta o visita general</option>
                                <option value="Reconsulta_0_7">📅 Reconsulta (0-7 días) — Seguimiento dentro de 7 días</option>
                                <option value="Reconsulta_8_15">🕐 Reconsulta (8-15 días) — Seguimiento de 8 a 15 días</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">expand_more</span>
                        </div>
                        <p class="text-xs text-red-500 mt-1 hidden" id="err-tipo_cit"></p>
                    </div>

                    {{-- Motivo textarea --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Motivo de la consulta</label>
                        <div class="relative">
                            <textarea id="motivo" rows="4" maxlength="500"
                                      class="field-input resize-none"
                                      placeholder="Describe brevemente el motivo de la consulta del paciente..."
                                      oninput="updateCounter()"></textarea>
                            <span class="absolute bottom-2 right-3 text-[11px] text-gray-400" id="motivo-counter">0 / 500</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-7 py-4 flex justify-between items-center border-t border-gray-100">
                    <button onclick="goToStep(1)" class="px-5 py-2 text-gray-500 font-semibold hover:text-[#1A3A6B] border border-gray-300 rounded-lg flex items-center gap-1 text-sm">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Atrás
                    </button>
                    <button id="btn-paso2" onclick="submitPaso2()" disabled
                            class="px-7 py-2.5 bg-gray-200 text-gray-400 rounded-lg font-bold flex items-center gap-2 cursor-not-allowed transition-all text-sm">
                        Buscar Médicos <span class="material-symbols-outlined text-[18px]">search</span>
                    </button>
                </div>
            </section>

            {{-- ══════════════════════ PASO 3 ══════════════════════ --}}
            <section id="step-3" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-7">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-full bg-[#EEF4FF] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[#1A3A6B] text-[20px] ms-fill">medical_services</span>
                        </div>
                        <div>
                            <h2 class="font-bold text-[#1A3A6B] text-base tracking-wide">— MÉDICO DISPONIBLE —</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Selecciona el médico y horario disponible para la cita.</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 mb-5"></div>

                    {{-- Resumen de búsqueda --}}
                    <div id="search-summary" class="bg-[#F0F4FF] rounded-xl p-3 mb-5 flex items-center gap-2 flex-wrap">
                        <span class="material-symbols-outlined text-[#F5A623] text-[20px]">stethoscope</span>
                        <span class="text-sm font-semibold text-[#1A3A6B]">Médicos disponibles para</span>
                        <span id="badge-esp" class="bg-[#1A3A6B] text-white text-xs px-3 py-1 rounded-full font-semibold"></span>
                        <span class="text-gray-400 text-sm">·</span>
                        <span id="badge-count" class="text-sm text-gray-500"></span>
                        <span class="text-gray-400 text-sm">·</span>
                        {{-- Date picker para cambiar fecha --}}
                        <div class="flex items-center gap-1 ml-auto">
                            <label class="text-xs text-gray-500">Fecha:</label>
                            <input type="date" id="fecha-picker" value="{{ date('Y-m-d') }}"
                                   class="text-xs border border-gray-200 rounded-lg px-2 py-1 text-[#1A3A6B] font-semibold"
                                   onchange="reloadMedicos()"/>
                        </div>
                    </div>

                    {{-- Grid médicos --}}
                    <div id="medicos-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>

                    {{-- Estado vacío --}}
                    <div id="medicos-empty" class="hidden text-center py-10">
                        <span class="material-symbols-outlined text-gray-300 text-5xl">event_busy</span>
                        <p class="text-gray-500 font-semibold mt-2">No hay médicos disponibles para esta especialidad hoy.</p>
                        <p class="text-xs text-gray-400 mt-1">Selecciona otra fecha o consulta con el administrador.</p>
                    </div>

                    {{-- Loading --}}
                    <div id="medicos-loading" class="hidden text-center py-8">
                        <div class="inline-block w-8 h-8 border-4 border-[#1A3A6B] border-t-transparent rounded-full animate-spin"></div>
                        <p class="text-sm text-gray-400 mt-2">Consultando disponibilidad...</p>
                    </div>

                    {{-- Botón confirmar --}}
                    <div class="mt-6">
                        <button id="btn-confirm" disabled onclick="openConfirmModal()">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            Confirmar Cita
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 px-7 py-4 flex justify-between items-center border-t border-gray-100">
                    <button onclick="goToStep(2)" class="px-5 py-2 text-gray-500 font-semibold hover:text-[#1A3A6B] border border-gray-300 rounded-lg flex items-center gap-1 text-sm">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Atrás
                    </button>
                </div>
            </section>

            {{-- ══════════════════════ ÉXITO ══════════════════════ --}}
            <section id="step-success" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5 check-anim">
                    <span class="material-symbols-outlined text-green-600 text-5xl ms-fill">check_circle</span>
                </div>
                <h2 class="text-2xl font-bold text-[#1A3A6B] mb-2">¡Cita Registrada Exitosamente!</h2>
                <p class="text-gray-500 text-sm mb-6">La cita ha sido agendada con estado <strong>Reservado</strong>.</p>

                <div class="bg-[#F0F4FF] rounded-xl p-5 text-left max-w-sm mx-auto mb-6 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Paciente</span>
                        <span class="font-semibold text-[#1A3A6B]" id="s-paciente"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Médico</span>
                        <span class="font-semibold text-[#1A3A6B]" id="s-medico"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Especialidad</span>
                        <span class="font-semibold text-[#1A3A6B]" id="s-especialidad"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Fecha</span>
                        <span class="font-semibold text-[#1A3A6B]" id="s-fecha"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Hora</span>
                        <span class="font-semibold text-[#1A3A6B]" id="s-hora"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Tipo</span>
                        <span class="font-semibold text-[#1A3A6B]" id="s-tipo"></span>
                    </div>
                </div>

                <div class="flex gap-3 justify-center">
                    <button onclick="resetWizard()"
                            class="px-6 py-2.5 bg-[#1A3A6B] text-white rounded-lg font-semibold text-sm hover:bg-[#0f2a5e] transition-all">
                        + Registrar otra consulta
                    </button>
                    <a href="{{ route('gestora.consultas.historial') }}"
                       class="px-6 py-2.5 border border-gray-300 text-gray-600 rounded-lg font-semibold text-sm hover:border-[#1A3A6B] hover:text-[#1A3A6B] transition-all">
                        Ver historial
                    </a>
                </div>
            </section>

            {{-- Tips --}}
            <div id="tips-section" class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex flex-col gap-1">
                    <span class="material-symbols-outlined text-[#1A3A6B]">history</span>
                    <p class="text-xs font-bold text-[#1A3A6B]">Últimas Atenciones</p>
                    <p class="text-[11px] text-blue-500/70">Consulte el historial si el paciente ya está registrado en el sistema.</p>
                </div>
                <div class="bg-amber-50 p-4 rounded-xl border border-amber-100 flex flex-col gap-1">
                    <span class="material-symbols-outlined text-amber-600">info</span>
                    <p class="text-xs font-bold text-amber-700">Validación de CI</p>
                    <p class="text-[11px] text-amber-600/70">El número de carnet se valida automáticamente con la base de datos.</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-xl border border-gray-200 flex flex-col gap-1">
                    <span class="material-symbols-outlined text-gray-500">support_agent</span>
                    <p class="text-xs font-bold text-gray-600">Soporte TI</p>
                    <p class="text-[11px] text-gray-400">En caso de error de sistema, contacte a soporte interno (Int. 450).</p>
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

{{-- ════════════ MODAL CONFIRMACIÓN ════════════ --}}
<div id="confirm-modal" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-full bg-[#EEF4FF] flex items-center justify-center">
                <span class="material-symbols-outlined text-[#1A3A6B] ms-fill">event_available</span>
            </div>
            <div>
                <h3 class="font-bold text-[#1A3A6B] text-lg">Confirmar Cita</h3>
                <p class="text-xs text-gray-400">Revisa el resumen antes de registrar</p>
            </div>
        </div>
        <div class="space-y-3 bg-gray-50 rounded-xl p-4 mb-6 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Paciente</span>
                <span class="font-semibold text-[#1A3A6B]" id="m-paciente">—</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Especialidad</span>
                <span class="font-semibold text-[#1A3A6B]" id="m-especialidad">—</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Médico</span>
                <span class="font-semibold text-[#1A3A6B]" id="m-medico">—</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Fecha</span>
                <span class="font-semibold text-[#1A3A6B]" id="m-fecha">—</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Hora</span>
                <span class="font-semibold text-[#F5A623] text-base font-bold" id="m-hora">—</span>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="closeConfirmModal()"
                    class="flex-1 py-2.5 border border-gray-300 text-gray-600 rounded-lg font-semibold text-sm hover:border-red-300 hover:text-red-500 transition-all">
                Cancelar
            </button>
            <button id="btn-final-confirm" onclick="submitConfirmar()"
                    class="flex-1 py-2.5 bg-[#1A3A6B] text-white rounded-lg font-semibold text-sm hover:bg-[#0f2a5e] transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check</span>
                Confirmar y Registrar
            </button>
        </div>
    </div>
</div>

{{-- ════════════ MODAL CI DUPLICADO ════════════ --}}
<div id="dup-modal" style="display:none;" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[200]">
    <div class="bg-white rounded-2xl shadow-2xl p-7 max-w-sm w-full mx-4">
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-amber-500 text-3xl ms-fill">warning</span>
            <h3 class="font-bold text-[#1A3A6B] text-base">CI ya registrado</h3>
        </div>
        <p class="text-sm text-gray-600 mb-1">Este CI ya está registrado en el sistema:</p>
        <div class="bg-amber-50 rounded-lg p-3 mb-5 border border-amber-200">
            <p class="font-bold text-[#1A3A6B]" id="dup-nombre"></p>
            <p class="text-xs text-gray-500 mt-0.5">CI: <span id="dup-ci"></span></p>
        </div>
        <p class="text-sm text-gray-500 mb-4">¿Deseas usar este paciente o registrar uno nuevo con los datos ingresados?</p>
        <div class="flex flex-col gap-2">
            <button id="btn-usar-existente" onclick="usarExistente()"
                    class="w-full py-2.5 bg-[#1A3A6B] text-white rounded-lg font-semibold text-sm hover:bg-[#0f2a5e] flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">person_check</span>
                Usar paciente existente
            </button>
            <button onclick="registrarNuevo()"
                    class="w-full py-2.5 border border-gray-300 text-gray-600 rounded-lg font-semibold text-sm hover:border-[#1A3A6B] hover:text-[#1A3A6B] flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Registrar como nuevo
            </button>
        </div>
    </div>
</div>

{{-- ════════════ JAVASCRIPT WIZARD ════════════ --}}
<script>
// ── Sidebar toggle ────────────────────────────────────────────────────────
function toggleSidebar() {
    const sidebar   = document.getElementById('sidebar');
    const main      = document.getElementById('main-wrapper');
    const icon      = document.getElementById('toggle-icon');
    const collapsed = sidebar.classList.toggle('collapsed');
    main.classList.toggle('sidebar-collapsed', collapsed);
    icon.textContent = collapsed ? 'menu' : 'menu_open';
    localStorage.setItem('sidebar_collapsed', collapsed ? '1' : '0');
}
// Restaurar estado guardado
(function () {
    if (localStorage.getItem('sidebar_collapsed') === '1') {
        document.getElementById('sidebar').classList.add('collapsed');
        document.getElementById('main-wrapper').classList.add('sidebar-collapsed');
        const icon = document.getElementById('toggle-icon');
        if (icon) icon.textContent = 'menu';
    }
})();

// ── Estado global ─────────────────────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let pasoActual = 1;
let selectedMedId   = null;
let selectedMedNom  = null;
let selectedSlot    = null;
let selectedFecha   = document.getElementById('fecha-picker')?.value ?? new Date().toISOString().slice(0,10);
let datosDupPac     = null;  // datos del paciente duplicado
let nuevosDatos     = null;  // datos que el usuario ingresó

// ── Fetch helper ──────────────────────────────────────────────────────────
async function postJSON(url, body) {
    const r = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept':        'application/json',
            'X-CSRF-TOKEN':  CSRF,
        },
        body: JSON.stringify(body),
    });
    const data = await r.json();
    return { ok: r.ok, status: r.status, data };
}

// ── Stepper UI ────────────────────────────────────────────────────────────
function goToStep(n) {
    [1,2,3].forEach(i => {
        document.getElementById(`step-${i}`).classList.add('hidden');
        document.getElementById(`circle-${i}`).className = 'step-circle ' + (i < n ? 'done' : i === n ? 'active' : 'pending');
        const lbl = document.getElementById(`label-${i}`);
        if (lbl) lbl.className = 'text-xs mt-1 text-center ' + (i <= n ? 'font-semibold text-[#1A3A6B]' : 'font-semibold text-gray-400');
        if (i < 3) {
            const line = document.getElementById(`line-${i}`);
            if (line) line.className = 'step-line flex-1 ' + (i < n ? 'done' : 'pending');
        }
    });
    document.getElementById(`step-${n}`).classList.remove('hidden');
    // Cambiar iconos de círculos completados a checkmark
    for (let i = 1; i < n; i++) {
        document.getElementById(`circle-${i}`).innerHTML = '<span class="material-symbols-outlined text-[20px] ms-fill">check</span>';
    }
    pasoActual = n;
    if (n < 4) {
        document.getElementById('tips-section')?.classList.remove('hidden');
        document.getElementById('step-success')?.classList.add('hidden');
    }
}

// ── PASO 1 — validación inline ────────────────────────────────────────────
function checkPaso1() {
    const nom  = document.getElementById('nom_pac').value.trim();
    const ci   = document.getElementById('ci_pac').value.trim();
    const seg  = document.getElementById('seguro_pac').value;
    const btn  = document.getElementById('btn-paso1');
    const ok   = nom && ci && seg;
    btn.disabled = !ok;
    btn.className = ok
        ? 'px-7 py-2.5 bg-[#1A3A6B] text-white rounded-lg font-bold flex items-center gap-2 transition-all text-sm hover:bg-[#0f2a5e]'
        : 'px-7 py-2.5 bg-gray-200 text-gray-400 rounded-lg font-bold flex items-center gap-2 cursor-not-allowed transition-all text-sm';
}
['nom_pac','ci_pac','seguro_pac'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', checkPaso1);
    document.getElementById(id)?.addEventListener('change', checkPaso1);
});

// ── PASO 1 — submit ───────────────────────────────────────────────────────
async function submitPaso1() {
    clearErrors1();
    const body = {
        nom_pac:    document.getElementById('nom_pac').value.trim(),
        apat_pac:   document.getElementById('apat_pac').value.trim(),
        ci_pac:     document.getElementById('ci_pac').value.trim(),
        tel_pac:    '+591 ' + document.getElementById('tel_pac').value.trim(),
        seguro_pac: document.getElementById('seguro_pac').value,
        priv_pac:   document.getElementById('priv_pac').value === '1' ? 1 : 0,
    };

    const btn = document.getElementById('btn-paso1');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">refresh</span> Verificando...';

    const { ok, status, data } = await postJSON('{{ route("gestora.consultas.paso1") }}', body);

    btn.disabled = false;
    btn.innerHTML = 'Continuar <span class="material-symbols-outlined text-[18px]">arrow_forward</span>';

    if (status === 422) {
        showErrors1(data.errors); return;
    }

    if (data.ci_duplicado) {
        datosDupPac = data.paciente_existente;
        nuevosDatos = body;
        document.getElementById('dup-nombre').textContent = datosDupPac.nombre;
        document.getElementById('dup-ci').textContent     = datosDupPac.ci_pac;
        document.getElementById('dup-modal').style.display = 'flex';
        return;
    }

    goToStep(2);
}

function clearErrors1() {
    ['nom_pac','ci_pac','seguro_pac'].forEach(f => {
        const el = document.getElementById('err-' + f);
        if (el) { el.textContent = ''; el.classList.add('hidden'); }
        document.getElementById(f)?.classList.remove('error');
    });
}

function showErrors1(errors) {
    Object.entries(errors).forEach(([k, msgs]) => {
        const el = document.getElementById('err-' + k);
        if (el) { el.textContent = msgs[0]; el.classList.remove('hidden'); }
        document.getElementById(k)?.classList.add('error');
    });
}

// ── CI Duplicado ──────────────────────────────────────────────────────────
async function usarExistente() {
    document.getElementById('dup-modal').style.display = 'none';
    const { ok, data } = await postJSON('{{ route("gestora.consultas.existente") }}', { id_pac: datosDupPac.id_pac });
    if (ok) goToStep(2);
}

function registrarNuevo() {
    document.getElementById('dup-modal').style.display = 'none';
    // Clear CI field so the user can enter a different one, or proceed as new
    // We already have the data in session from the first POST (without ci_duplicado blocking)
    // Simply advance to step 2 (backend already saved paso1 before returning ci_duplicado)
    goToStep(2);
}

// ── Toggle consulta privada ───────────────────────────────────────────────
function togglePrivada() {
    const track = document.getElementById('toggle-track');
    const input = document.getElementById('priv_pac');
    const row   = document.getElementById('toggle-row');
    const isOn  = track.classList.toggle('on');
    input.value = isOn ? '1' : '0';
    if (isOn) {
        row.classList.add('bg-blue-50', 'border-[#1A3A6B]', 'border-l-4');
        row.classList.remove('bg-gray-50', 'border-gray-200');
    } else {
        row.classList.remove('bg-blue-50', 'border-[#1A3A6B]', 'border-l-4');
        row.classList.add('bg-gray-50', 'border-gray-200');
    }
}

// ── Especialidad dropdown ─────────────────────────────────────────────────
function toggleEspPanel() {
    document.getElementById('esp-panel').classList.toggle('open');
    document.getElementById('esp-search')?.focus();
}

function filterEsp(q) {
    const items = document.querySelectorAll('#esp-list .esp-option');
    items.forEach(li => {
        li.style.display = li.dataset.name.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
    });
}

function selectEsp(id, name) {
    document.getElementById('id_esp').value     = id;
    document.getElementById('esp-label').textContent = name;
    document.getElementById('esp-label').classList.remove('text-gray-400');
    document.getElementById('esp-label').classList.add('text-gray-800');
    document.getElementById('esp-panel').classList.remove('open');
    checkPaso2();
}

document.addEventListener('click', e => {
    const panel   = document.getElementById('esp-panel');
    const trigger = document.getElementById('esp-trigger');
    if (panel && !panel.contains(e.target) && !trigger.contains(e.target)) {
        panel.classList.remove('open');
    }
});

// ── PASO 2 — validación ───────────────────────────────────────────────────
function checkPaso2() {
    const esp  = document.getElementById('id_esp').value;
    const tipo = document.getElementById('tipo_cit').value;
    const btn  = document.getElementById('btn-paso2');
    const ok   = esp && tipo;
    btn.disabled = !ok;
    btn.className = ok
        ? 'px-7 py-2.5 bg-[#1A3A6B] text-white rounded-lg font-bold flex items-center gap-2 transition-all text-sm hover:bg-[#0f2a5e]'
        : 'px-7 py-2.5 bg-gray-200 text-gray-400 rounded-lg font-bold flex items-center gap-2 cursor-not-allowed transition-all text-sm';
}

function updateCounter() {
    const len = document.getElementById('motivo').value.length;
    const ctr = document.getElementById('motivo-counter');
    ctr.textContent = len + ' / 500';
    ctr.classList.toggle('text-[#1A3A6B]', len > 0);
    ctr.classList.toggle('text-gray-400', len === 0);
}

// ── PASO 2 — submit ───────────────────────────────────────────────────────
async function submitPaso2() {
    const btn = document.getElementById('btn-paso2');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">refresh</span> Buscando...';

    const { ok, status, data } = await postJSON('{{ route("gestora.consultas.paso2") }}', {
        id_esp:   document.getElementById('id_esp').value,
        tipo_cit: document.getElementById('tipo_cit').value,
        motivo:   document.getElementById('motivo').value.trim(),
        fecha:    selectedFecha,
    });

    btn.disabled = false;
    btn.innerHTML = 'Buscar Médicos <span class="material-symbols-outlined text-[18px]">search</span>';

    if (!ok) { Swal.fire('Error', data.errors ? Object.values(data.errors)[0][0] : 'Error al procesar.', 'error'); return; }

    goToStep(3);
    renderMedicos(data.disponibilidad);
}

// ── PASO 3 — render médicos ───────────────────────────────────────────────
function renderMedicos(disp) {
    document.getElementById('badge-esp').textContent   = disp.especialidad;
    document.getElementById('badge-count').textContent = disp.total_medicos + ' médico(s) · ' + disp.dia;

    const grid    = document.getElementById('medicos-grid');
    const empty   = document.getElementById('medicos-empty');
    const loading = document.getElementById('medicos-loading');

    loading.classList.add('hidden');
    grid.innerHTML = '';
    selectedMedId = null; selectedMedNom = null; selectedSlot = null;
    updateConfirmBtn();

    if (!disp.medicos || disp.medicos.length === 0) {
        grid.classList.add('hidden'); empty.classList.remove('hidden'); return;
    }
    grid.classList.remove('hidden'); empty.classList.add('hidden');

    disp.medicos.forEach(med => {
        const slotsHtml = med.slots.map(s =>
            `<span class="slot-chip ${s.disponible ? '' : 'occupied'}"
                   onclick="${s.disponible ? `selectSlot(this,'${med.id_med}','${encodeURIComponent(med.nombre_completo)}','${s.hora}')` : ''}"
                   title="${s.disponible ? '' : 'Sin disponibilidad'}">${s.hora}</span>`
        ).join('');

        const card = document.createElement('div');
        card.className = 'doctor-card';
        card.id = `card-med-${med.id_med}`;
        card.innerHTML = `
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl flex-shrink-0"
                     style="background:linear-gradient(135deg,#1E4B8F,#2E6CC7)">
                    ${med.inicial}
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-[#1A3A6B] text-sm leading-tight truncate">${med.nombre_completo}</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <span class="w-2 h-2 rounded-full ${med.tiene_disponibilidad ? 'bg-green-500' : 'bg-gray-300'} flex-shrink-0"></span>
                        <span class="text-xs text-gray-500">${disp.especialidad}</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-100 my-3"></div>
            <div class="flex items-center gap-1 mb-2">
                <span class="material-symbols-outlined text-gray-400 text-[16px]">schedule</span>
                <span class="text-xs text-gray-500 font-medium">Horarios disponibles hoy</span>
            </div>
            <div class="flex flex-wrap gap-2">${slotsHtml}</div>
            ${med.tiene_disponibilidad ? '' : '<p class="text-xs text-gray-400 mt-2 text-center">Sin disponibilidad para hoy</p>'}
        `;
        grid.appendChild(card);
    });
}

async function reloadMedicos() {
    selectedFecha = document.getElementById('fecha-picker').value;
    selectedMedId = null; selectedMedNom = null; selectedSlot = null;
    updateConfirmBtn();

    document.getElementById('medicos-grid').innerHTML = '';
    document.getElementById('medicos-grid').classList.add('hidden');
    document.getElementById('medicos-empty').classList.add('hidden');
    document.getElementById('medicos-loading').classList.remove('hidden');

    const idEsp = document.getElementById('id_esp').value;
    const r = await fetch(`{{ route('gestora.consultas.medicos') }}?id_esp=${idEsp}&fecha=${selectedFecha}`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    });
    const data = await r.json();
    renderMedicos(data);
}

function selectSlot(el, medId, medNomEnc, hora) {
    // Deselect previous card
    if (selectedMedId && selectedMedId != medId) {
        document.querySelectorAll('.slot-chip.selected').forEach(c => c.classList.remove('selected'));
        const prevCard = document.getElementById(`card-med-${selectedMedId}`);
        if (prevCard) {
            prevCard.classList.remove('selected');
            const badge = prevCard.querySelector('.selected-badge');
            if (badge) badge.remove();
        }
    }

    // Toggle slot
    const card = document.getElementById(`card-med-${medId}`);
    const wasSelected = el.classList.contains('selected');

    document.querySelectorAll(`#card-med-${medId} .slot-chip`).forEach(c => c.classList.remove('selected'));

    if (wasSelected) {
        selectedSlot = null; selectedMedId = null; selectedMedNom = null;
        card.classList.remove('selected');
        card.querySelector('.selected-badge')?.remove();
    } else {
        el.classList.add('selected');
        selectedSlot  = hora;
        selectedMedId = medId;
        selectedMedNom = decodeURIComponent(medNomEnc);

        card.classList.add('selected');
        if (!card.querySelector('.selected-badge')) {
            const badge = document.createElement('div');
            badge.className = 'selected-badge';
            badge.innerHTML = 'Seleccionado ✓';
            card.appendChild(badge);
        }
    }
    updateConfirmBtn();
}

function updateConfirmBtn() {
    const btn = document.getElementById('btn-confirm');
    const ok  = selectedMedId && selectedSlot;
    btn.disabled = !ok;
}

// ── Confirm modal ─────────────────────────────────────────────────────────
function openConfirmModal() {
    const nom = document.getElementById('nom_pac').value.trim();
    const apa = document.getElementById('apat_pac').value.trim();
    const esp = document.getElementById('esp-label').textContent;
    const fec = document.getElementById('fecha-picker').value;

    document.getElementById('m-paciente').textContent   = (nom + ' ' + apa).trim();
    document.getElementById('m-especialidad').textContent = esp;
    document.getElementById('m-medico').textContent     = selectedMedNom;
    document.getElementById('m-fecha').textContent      = new Date(fec + 'T00:00:00').toLocaleDateString('es-BO', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
    document.getElementById('m-hora').textContent       = selectedSlot;
    document.getElementById('confirm-modal').style.display = 'flex';
}

function closeConfirmModal() {
    document.getElementById('confirm-modal').style.display = 'none';
}

// ── POST confirmar ────────────────────────────────────────────────────────
async function submitConfirmar() {
    const btn = document.getElementById('btn-final-confirm');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">refresh</span> Registrando...';

    const { ok, status, data } = await postJSON('{{ route("gestora.consultas.confirmar") }}', {
        id_med:    selectedMedId,
        hora_cit:  selectedSlot,
        fecha_cit: document.getElementById('fecha-picker').value,
    });

    btn.disabled = false;
    btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">check</span> Confirmar y Registrar';

    if (status === 409 && data.slot_ocupado) {
        closeConfirmModal();
        Swal.fire({ icon:'warning', title:'Horario tomado', text: data.message, confirmButtonColor:'#1A3A6B' });
        reloadMedicos();
        return;
    }

    if (!ok) {
        closeConfirmModal();
        Swal.fire({ icon:'error', title:'Error', text: data.message || 'Error al registrar la cita.', confirmButtonColor:'#1A3A6B' });
        return;
    }

    closeConfirmModal();
    showSuccess(data.cita);
}

function showSuccess(cita) {
    [1,2,3].forEach(i => document.getElementById(`step-${i}`).classList.add('hidden'));
    document.getElementById('tips-section')?.classList.add('hidden');

    document.getElementById('s-paciente').textContent    = cita.paciente;
    document.getElementById('s-medico').textContent      = cita.medico;
    document.getElementById('s-especialidad').textContent= cita.especialidad;
    document.getElementById('s-fecha').textContent       = cita.fecha;
    document.getElementById('s-hora').textContent        = cita.hora;
    document.getElementById('s-tipo').textContent        = cita.tipo;

    document.getElementById('step-success').classList.remove('hidden');
    // Update stepper to all done
    [1,2,3].forEach(i => {
        const c = document.getElementById(`circle-${i}`);
        c.className = 'step-circle done';
        c.innerHTML = '<span class="material-symbols-outlined text-[20px] ms-fill">check</span>';
    });
    ['line-1','line-2'].forEach(id => {
        const l = document.getElementById(id);
        if (l) l.className = 'step-line flex-1 done';
    });
}

function resetWizard() {
    // Reset form fields
    ['nom_pac','apat_pac','ci_pac','tel_pac','motivo'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('seguro_pac').value = '';
    document.getElementById('id_esp').value     = '';
    document.getElementById('tipo_cit').value   = '';
    document.getElementById('priv_pac').value   = '0';
    document.getElementById('esp-label').textContent = 'Seleccionar especialidad...';
    document.getElementById('esp-label').classList.add('text-gray-400');
    document.getElementById('esp-label').classList.remove('text-gray-800');

    const track = document.getElementById('toggle-track');
    if (track.classList.contains('on')) togglePrivada();

    selectedMedId = null; selectedMedNom = null; selectedSlot = null;
    datosDupPac   = null; nuevosDatos    = null;

    document.getElementById('tips-section')?.classList.remove('hidden');
    document.getElementById('step-success').classList.add('hidden');
    goToStep(1);
}

function cancelar() {
    Swal.fire({
        title: '¿Cancelar registro?',
        text: 'Se perderán los datos ingresados.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor:  '#1A3A6B',
        confirmButtonText:  'Sí, cancelar',
        cancelButtonText:   'No, continuar',
    }).then(r => { if (r.isConfirmed) resetWizard(); });
}
</script>
</body>
</html>
