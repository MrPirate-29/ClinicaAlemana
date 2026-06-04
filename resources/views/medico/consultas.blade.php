<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Mis Consultas — CRM Centro Médico</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
        .ms-fill { font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }

        /* ── Sidebar ─────────────────────────────────────────── */
        #sidebar { width: 240px; transition: width 0.3s ease; }
        #sidebar.collapsed { width: 64px; }
        .sidebar-label { white-space: nowrap; overflow: hidden; transition: opacity 0.15s; flex-shrink: 0; }
        #sidebar.collapsed .sidebar-label { opacity: 0; max-width: 0; pointer-events: none; }
        #sidebar.collapsed .sidebar-badge { display: none; }
        #sidebar.collapsed .sidebar-link { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
        .nav-active { box-shadow: inset 3px 0 0 #F5A623; background: rgba(255,255,255,0.12); color: #F5A623 !important; }
        .nav-item { position: relative; }
        .nav-item .tooltip-sb {
            position: absolute; left: 72px; top: 50%; transform: translateY(-50%);
            background: #0f2a5e; color: #fff; padding: 4px 10px;
            border-radius: 6px; font-size: 12px; white-space: nowrap;
            opacity: 0; pointer-events: none; transition: opacity 0.15s; z-index: 200;
        }
        #sidebar.collapsed .nav-item:hover .tooltip-sb { opacity: 1; }
        #main-wrapper { margin-left: 240px; transition: margin-left 0.3s ease; }
        #main-wrapper.sidebar-collapsed { margin-left: 64px; }
        .sidebar-logo { display: block; flex-shrink: 0; max-height: 36px; width: auto; }
        #sidebar.collapsed .sidebar-logo { display: none; }
        .sidebar-icon-only { display: none !important; }
        #sidebar.collapsed .sidebar-icon-only { display: flex !important; align-items: center; }

        /* ── Campos ──────────────────────────────────────────── */
        .field-input {
            border: 1.5px solid #E5E7EB; border-radius: 8px; padding: 8px 12px;
            font-size: 13px; font-family: 'Poppins', sans-serif; outline: none;
            transition: border-color .2s, box-shadow .2s; background: #fff;
            height: 44px; width: 100%;
        }
        .field-input:focus { border-color: #1A3A6B; box-shadow: 0 0 0 3px rgba(26,58,107,0.1); }
        .field-input:disabled { background: #F9FAFB; cursor: not-allowed; color: #9CA3AF; }
        textarea.field-input { height: auto; }

        /* ── Paginación ──────────────────────────────────────── */
        .page-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 6px; font-size: 13px;
            font-weight: 500; transition: all .15s;
        }
        .page-btn.active { background: #1A3A6B; color: #fff; }
        .page-btn:not(.active):hover { background: #EEF4FF; color: #1A3A6B; }
        .page-btn.disabled { opacity: 0.35; cursor: not-allowed; }

        /* ── Estado badges ───────────────────────────────────── */
        .est-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
        }
        .est-reportado   { background:#E8F5E9; color:#2E7D32; }
        .est-pendiente   { background:#FFF8E1; color:#F57F17; }
        .est-alerta      { background:#FFEBEE; color:#D94040; }
        .est-fuga        { background:#FCE4EC; color:#C62828; }
        .est-retorno     { background:#E8F5E9; color:#1B5E20; }

        /* Filas especiales */
        .row-alerta { background: #FFF5F5 !important; border-left: 3px solid #D94040; }
        .row-fuga   { background: #FFF0F0 !important; }

        /* ── Especialidad badges ─────────────────────────────── */
        .esp-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
        .esp-psiquiatria   { background:#EDE7F6; color:#4527A0; }
        .esp-pediatria     { background:#E3F2FD; color:#0D47A1; }
        .esp-ginecologia   { background:#FCE4EC; color:#880E4F; }
        .esp-cardiologia   { background:#FFEBEE; color:#B71C1C; }
        .esp-traumatologia { background:#E8F5E9; color:#1B5E20; }
        .esp-gastro        { background:#FFF3E0; color:#E65100; }
        .esp-default       { background:#EEF4FF; color:#1A3A6B; }

        /* ── Drawer ──────────────────────────────────────────── */
        #drawer-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.4);
            z-index: 400; opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
        }
        #drawer-overlay.open { opacity: 1; pointer-events: all; }
        #drawer-panel {
            position: fixed; top: 0; right: 0; width: 480px; height: 100vh;
            background: #fff; z-index: 500;
            box-shadow: -8px 0 32px rgba(0,0,0,0.15);
            transform: translateX(100%); transition: transform 0.3s ease;
            display: flex; flex-direction: column;
        }
        #drawer-panel.open { transform: translateX(0); }

        /* Asistencia buttons */
        .asist-btn {
            flex: 1; height: 52px; border-radius: 8px; border: 2px solid #E5E7EB;
            font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 500;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            gap: 8px; transition: all .2s; background: #fff;
        }
        .asist-btn.si-active   { background:#E8F5E9; border-color:#2E7D32; color:#1B5E20; font-weight: 600; }
        .asist-btn.no-active   { background:#FFEBEE; border-color:#D94040; color:#C62828; font-weight: 600; }

        /* Save button disabled */
        #btn-guardar:disabled { background: #D1D5DB; cursor: not-allowed; }

        /* Motivo section slide */
        #motivo-section { display: none; }

        /* KPI card top border */
        .kpi-border-blue   { border-top: 4px solid #1A3A6B; }
        .kpi-border-orange { border-top: 4px solid #F5A623; }
        .kpi-border-red    { border-top: 4px solid #D94040; }
    </style>
</head>
<body class="bg-[#F7F8FA] min-h-screen flex overflow-hidden">

{{-- ═══════════════════════════════════ SIDEBAR ═══════════════════════════════════ --}}
<aside id="sidebar" class="fixed left-0 top-0 h-screen bg-[#1A3A6B] flex flex-col z-50 collapsed">

    {{-- Logo + toggle --}}
    <div class="flex items-center justify-between px-3 py-3 border-b border-white/10 min-h-[64px]">
        <div class="flex items-center gap-2 overflow-hidden">
            <img src="{{ asset('img/logo-alemana.jpg') }}" alt="Clínica Alemana"
                 class="sidebar-logo rounded bg-white px-1.5 py-0.5">
            {{-- Ícono cruz blanca (modo colapsado) --}}
            <span class="material-symbols-outlined text-white text-2xl ms-fill sidebar-icon-only">local_hospital</span>
        </div>
        <button onclick="toggleSidebar()"
                class="w-8 h-8 flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 rounded-lg transition-all flex-shrink-0 ml-1">
            <span class="material-symbols-outlined text-[20px]" id="toggle-icon">menu</span>
        </button>
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 py-4 flex flex-col gap-1 overflow-hidden">

        {{-- Inicio --}}
        <div class="nav-item">
            <a href="{{ route('medico.panel') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 text-white/70 hover:text-white hover:bg-white/10 transition-all rounded-none text-sm font-medium">
                <span class="material-symbols-outlined text-[22px] flex-shrink-0">home</span>
                <span class="sidebar-label">Inicio</span>
            </a>
            <span class="tooltip-sb">Inicio</span>
        </div>

        {{-- Mis Consultas (activo) --}}
        <div class="nav-item">
            <a href="{{ route('medico.consultas') }}"
               class="sidebar-link nav-active flex items-center gap-3 px-4 py-3 text-white transition-all rounded-none text-sm font-medium">
                <span class="material-symbols-outlined text-[22px] flex-shrink-0 ms-fill">assignment_turned_in</span>
                <span class="sidebar-label">Mis Consultas</span>
            </a>
            <span class="tooltip-sb">Mis Consultas</span>
        </div>

        {{-- Alertas --}}
        @php $badgeAlertas = $alertasActivas > 0 ? $alertasActivas : null; @endphp
        <div class="nav-item">
            <a href="{{ route('medico.alertas') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 text-white/70 hover:text-white hover:bg-white/10 transition-all rounded-none text-sm font-medium relative">
                <span class="relative flex-shrink-0">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    @if($badgeAlertas)
                    <span class="sidebar-badge absolute -top-1 -right-1 bg-[#D94040] text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center leading-none">
                        {{ $badgeAlertas > 9 ? '9+' : $badgeAlertas }}
                    </span>
                    @endif
                </span>
                <span class="sidebar-label">Alertas</span>
                @if($badgeAlertas)
                <span class="sidebar-badge ml-auto bg-[#D94040] text-white text-[10px] font-bold rounded-full px-1.5 py-0.5">{{ $badgeAlertas }}</span>
                @endif
            </a>
            <span class="tooltip-sb">Alertas{{ $badgeAlertas ? " ({$badgeAlertas})" : '' }}</span>
        </div>

    </nav>

    {{-- Avatar médico (cerrar sesión) --}}
    <div class="border-t border-white/10 p-3 flex justify-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Cerrar sesión"
                    class="w-8 h-8 rounded-full bg-[#F5A623] flex items-center justify-center text-white font-bold text-sm hover:bg-[#d4881c] transition-all">
                {{ strtoupper(substr($usuario->nom_usu ?? 'M', 0, 1)) }}
            </button>
        </form>
    </div>
</aside>

{{-- ═══════════════════════════════ MAIN WRAPPER ═══════════════════════════════════ --}}
<div id="main-wrapper" class="flex-1 flex flex-col min-h-screen sidebar-collapsed">

    {{-- ══════════════════════════════ HEADER ═════════════════════════════════ --}}
    <header class="sticky top-0 z-40 bg-white border-b border-[#E5E7EB] h-[60px] flex items-center px-6 gap-4"
            style="box-shadow: 0 1px 4px rgba(0,0,0,0.06);">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-1 text-sm">
            <span class="text-[#9CA3AF]">Panel Médico</span>
            <span class="text-[#9CA3AF] mx-1">›</span>
            <span class="font-bold text-[#1A3A6B]">Mis Consultas</span>
        </div>

        {{-- Badge sucursal --}}
        <div class="hidden md:flex items-center gap-1 bg-[#EEF4FF] text-[#1A3A6B] text-xs font-medium px-3 py-1 rounded-full border border-[#C7D7F0] mx-auto">
            <span class="material-symbols-outlined text-[14px] ms-fill">location_on</span>
            Centro Médico — Achumani
        </div>

        {{-- Filtro período --}}
        <form method="GET" action="{{ route('medico.consultas') }}" class="flex items-center gap-2 ml-auto">
            <div class="flex items-center gap-1 border border-[#E5E7EB] rounded-lg px-3 py-1.5 text-sm text-gray-600 bg-white hover:border-[#1A3A6B] transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[16px] text-[#1A3A6B]">calendar_month</span>
                <input type="month" name="mes"
                       value="{{ $inicio->format('Y-m') }}"
                       onchange="this.form.submit()"
                       class="border-none outline-none text-xs text-[#1A3A6B] font-medium bg-transparent cursor-pointer w-28"/>
            </div>
            {{-- Preservar filtros --}}
            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
        </form>

        {{-- Badge rol + usuario --}}
        <div class="flex items-center gap-3">
            <span class="hidden sm:block bg-[#E8F5E9] text-[#2E7D32] text-xs font-medium px-3 py-1 rounded-full border border-[#A5D6A7]">
                Médico
            </span>
            <div class="flex items-center gap-2">
                <span class="hidden md:block font-bold text-sm text-[#1A3A6B] leading-none">
                    {{ trim(($usuario->nom_usu ?? '') . ' ' . ($usuario->apat_usu ?? '')) ?: 'Dr. —' }}
                </span>
                <div class="w-8 h-8 rounded-full bg-[#1A3A6B] flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr($usuario->nom_usu ?? 'M', 0, 1)) }}
                </div>
            </div>
        </div>
    </header>

    {{-- ════════════════════════════ CONTENIDO ════════════════════════════════ --}}
    <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-[1200px] mx-auto space-y-5">

            {{-- ─── BLOQUE 1: KPI Cards ──────────────────────────────────── --}}
            <div class="grid grid-cols-3 gap-4">

                {{-- Card 1: Consultas del mes --}}
                <div class="bg-white rounded-xl kpi-border-blue p-5 flex items-center gap-4"
                     style="box-shadow: 0 2px 8px rgba(0,0,0,0.06); height: 100px;">
                    <div class="w-11 h-11 rounded-full bg-[#EEF4FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#1A3A6B] ms-fill">calendar_month</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-[#1A3A6B] leading-none" style="font-size: 32px;">{{ $totalConsultas }}</p>
                        <p class="text-[#6B7280] text-[13px] leading-tight mt-0.5">Consultas este mes</p>
                        <p class="text-[11px] text-[#9CA3AF] mt-0.5">
                            Período: {{ $inicio->format('d') }} — {{ $fin->format('d M Y') }}
                        </p>
                    </div>
                </div>

                {{-- Card 2: Pendientes de reporte --}}
                <div class="bg-white rounded-xl kpi-border-orange p-5 flex items-center gap-4"
                     style="box-shadow: 0 2px 8px rgba(0,0,0,0.06); height: 100px;">
                    <div class="w-11 h-11 rounded-full bg-[#FFF8E1] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#F5A623] ms-fill">schedule</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-[#F5A623] leading-none" style="font-size: 32px;">{{ $pendientes }}</p>
                        <p class="text-[#6B7280] text-[13px] leading-tight mt-0.5">Pendientes de reporte</p>
                        @if($pendientes > 0)
                        <p class="text-[11px] text-[#F57F17] mt-0.5 font-medium">Requieren atención hoy</p>
                        @else
                        <p class="text-[11px] text-[#9CA3AF] mt-0.5">Todo al día</p>
                        @endif
                    </div>
                </div>

                {{-- Card 3: Alertas activas --}}
                <div class="bg-white rounded-xl kpi-border-red p-5 flex items-center gap-4"
                     style="box-shadow: 0 2px 8px rgba(0,0,0,0.06); height: 100px;">
                    <div class="w-11 h-11 rounded-full bg-[#FFEBEE] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#D94040] ms-fill">notifications_active</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-[#D94040] leading-none" style="font-size: 32px;">{{ $alertasActivas }}</p>
                        <p class="text-[#6B7280] text-[13px] leading-tight mt-0.5">Alertas de fuga</p>
                        @if($alertasActivas > 0)
                        <p class="text-[11px] text-[#D94040] mt-0.5 font-medium">Escalado al administrador</p>
                        @else
                        <p class="text-[11px] text-[#9CA3AF] mt-0.5">Sin alertas activas</p>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ─── BLOQUE 2: Tabla de Consultas ────────────────────────── --}}
            <div class="bg-white rounded-xl overflow-hidden" style="box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

                {{-- Header de la tabla --}}
                <div class="bg-[#F9FAFB] px-5 py-4 border-b border-[#E5E7EB]">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div>
                            <h2 class="font-semibold text-[#1A3A6B] text-base">Mis Consultas</h2>
                            <p class="text-gray-500 text-[13px] mt-0.5">Registro y seguimiento de tus pacientes</p>
                        </div>
                        {{-- Controles --}}
                        <form method="GET" action="{{ route('medico.consultas') }}"
                              class="flex items-center gap-2 flex-wrap">
                            @if(request('mes')) <input type="hidden" name="mes" value="{{ request('mes') }}"> @endif

                            {{-- Buscador --}}
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[16px] text-gray-400">search</span>
                                <input type="text" name="q" value="{{ request('q') }}"
                                       placeholder="Buscar paciente..."
                                       class="field-input pl-8 w-48" style="height: 38px; font-size: 13px;"/>
                            </div>

                            {{-- Filtro estado --}}
                            <select name="estado" onchange="this.form.submit()"
                                    class="field-input w-44" style="height: 38px; font-size: 13px;">
                                <option value="">Todos los estados</option>
                                <option value="Reportado"        {{ request('estado') === 'Reportado'        ? 'selected' : '' }}>Reportado</option>
                                <option value="Pendiente"        {{ request('estado') === 'Pendiente'        ? 'selected' : '' }}>Pendiente</option>
                                <option value="Alerta"           {{ request('estado') === 'Alerta'           ? 'selected' : '' }}>Alerta</option>
                                <option value="Fuga Confirmada"  {{ request('estado') === 'Fuga Confirmada'  ? 'selected' : '' }}>Fuga Confirmada</option>
                                <option value="Retorno Confirmado" {{ request('estado') === 'Retorno Confirmado' ? 'selected' : '' }}>Retorno Confirmado</option>
                            </select>

                            <button type="submit"
                                    class="flex items-center gap-1 bg-[#1A3A6B] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#0f2a5e] transition-all"
                                    style="height: 38px;">
                                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                                Filtrar
                            </button>
                            @if(request()->hasAny(['q','estado']))
                            <a href="{{ route('medico.consultas', array_filter(['mes' => request('mes')])) }}"
                               class="flex items-center gap-1 border border-gray-300 text-gray-500 px-3 py-2 rounded-lg text-sm font-semibold hover:border-red-300 hover:text-red-500 transition-all"
                               style="height: 38px;">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                                Limpiar
                            </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-[#6B7280] uppercase tracking-wide" style="width:120px;">Fecha</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-[#6B7280] uppercase tracking-wide" style="width:200px;">Paciente</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-[#6B7280] uppercase tracking-wide" style="width:150px;">Especialidad</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-[#6B7280] uppercase tracking-wide" style="width:160px;">Tipo</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-[#6B7280] uppercase tracking-wide" style="width:160px;">Próx. Reconsulta</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-[#6B7280] uppercase tracking-wide" style="width:160px;">Estado</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-[#6B7280] uppercase tracking-wide" style="width:120px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($consultas as $cita)
                        @php
                            $pac     = $cita->paciente;
                            $esp     = $cita->especialidad;
                            $estado  = $cita->estado_visual;

                            $rowClass = match($estado) {
                                'Alerta', 'Alerta — Sin reporte' => 'row-alerta',
                                'Fuga Confirmada'                => 'row-fuga',
                                default                          => '',
                            };

                            $badgeClass = match($estado) {
                                'Reportado'               => 'est-reportado',
                                'Pendiente'               => 'est-pendiente',
                                'Alerta','Alerta — Sin reporte' => 'est-alerta',
                                'Fuga Confirmada'         => 'est-fuga',
                                'Retorno Confirmado'      => 'est-retorno',
                                default                   => 'est-pendiente',
                            };

                            $badgeIcon = match($estado) {
                                'Reportado'               => 'check_circle',
                                'Pendiente'               => 'schedule',
                                'Alerta','Alerta — Sin reporte' => 'notifications_active',
                                'Fuga Confirmada'         => 'exit_to_app',
                                'Retorno Confirmado'      => 'how_to_reg',
                                default                   => 'schedule',
                            };

                            $tipoLabel = match($cita->tipo_cit) {
                                'Consulta'        => 'Consulta regular',
                                'Reconsulta_0_7'  => 'Reconsulta 0–7 días',
                                'Reconsulta_8_15' => 'Reconsulta 8–15 días',
                                'Campana'         => 'Campaña',
                                default           => $cita->tipo_cit,
                            };

                            $tipoIcon = match($cita->tipo_cit) {
                                'Consulta'        => 'local_hospital',
                                'Reconsulta_0_7'  => 'autorenew',
                                'Reconsulta_8_15' => 'timer',
                                'Campana'         => 'campaign',
                                default           => 'medical_services',
                            };

                            $tipoColor = match($cita->tipo_cit) {
                                'Reconsulta_0_7','Reconsulta_8_15' => 'text-[#F5A623]',
                                default => 'text-[#6B7280]',
                            };

                            $espNom = strtolower($esp->nom_esp ?? '');
                            $espClass = match(true) {
                                str_contains($espNom, 'psiquiat')   => 'esp-psiquiatria',
                                str_contains($espNom, 'pediatr')    => 'esp-pediatria',
                                str_contains($espNom, 'ginecol')    => 'esp-ginecologia',
                                str_contains($espNom, 'cardiol')    => 'esp-cardiologia',
                                str_contains($espNom, 'traumat')    => 'esp-traumatologia',
                                str_contains($espNom, 'gastro')     => 'esp-gastro',
                                default                             => 'esp-default',
                            };

                            $yaReportado = !is_null($cita->reporteConsulta);
                        @endphp
                        <tr class="border-b border-[#F3F4F6] hover:bg-[#FAFAFA] transition-colors cursor-pointer {{ $rowClass }}"
                            style="height: 64px;"
                            data-id="{{ $cita->id_cit }}">

                            {{-- Fecha --}}
                            <td class="px-5 py-3">
                                <p class="font-bold text-[#1A3A6B] text-sm">
                                    {{ \Carbon\Carbon::parse($cita->fec_cit)->format('d/m/Y') }}
                                </p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    {{ substr($cita->hora_cit ?? '', 0, 5) }}
                                </p>
                            </td>

                            {{-- Paciente --}}
                            <td class="px-5 py-3">
                                <p class="font-medium text-[#1A1A2E] text-sm leading-tight">
                                    {{ trim(($pac->nom_pac ?? '') . ' ' . ($pac->apat_pac ?? '')) ?: '—' }}
                                </p>
                                <p class="text-[11px] text-gray-400 mt-0.5">CI: {{ $pac->ci_pac ?? '—' }}</p>
                            </td>

                            {{-- Especialidad --}}
                            <td class="px-5 py-3">
                                <span class="esp-badge {{ $espClass }}">{{ $esp->nom_esp ?? '—' }}</span>
                            </td>

                            {{-- Tipo --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1 {{ $tipoColor }}">
                                    <span class="material-symbols-outlined text-[15px]">{{ $tipoIcon }}</span>
                                    <span class="text-[13px]">{{ $tipoLabel }}</span>
                                </div>
                            </td>

                            {{-- Próx. Reconsulta --}}
                            <td class="px-5 py-3">
                                @if($cita->prox_reconsulta)
                                    @if($cita->prox_vencida)
                                    <div class="flex items-center gap-1 text-[#D94040]">
                                        <span class="material-symbols-outlined text-[15px]">warning</span>
                                        <span class="text-[13px] font-medium">{{ $cita->prox_reconsulta }}</span>
                                    </div>
                                    @else
                                    <div class="flex items-center gap-1 text-[#1A3A6B]">
                                        <span class="material-symbols-outlined text-[15px]">event</span>
                                        <span class="text-[13px]">{{ $cita->prox_reconsulta }}</span>
                                    </div>
                                    @endif
                                @else
                                <span class="text-[12px] text-gray-400 italic">— No aplica —</span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="px-5 py-3">
                                <span class="est-badge {{ $badgeClass }}" id="estado-{{ $cita->id_cit }}">
                                    <span class="material-symbols-outlined text-[12px] ms-fill">{{ $badgeIcon }}</span>
                                    {{ $estado === 'Alerta — Sin reporte' ? 'Alerta' : $estado }}
                                </span>
                            </td>

                            {{-- Acción --}}
                            <td class="px-5 py-3">
                                @if($yaReportado)
                                <button type="button"
                                        onclick="abrirDrawer({{ $cita->id_cit }}, true)"
                                        class="border border-[#D1D5DB] text-gray-500 text-[12px] font-semibold px-3 py-1.5 rounded-md hover:border-[#1A3A6B] hover:text-[#1A3A6B] transition-all whitespace-nowrap">
                                    Ver reporte
                                </button>
                                @else
                                <button type="button"
                                        id="btn-action-{{ $cita->id_cit }}"
                                        onclick="abrirDrawer({{ $cita->id_cit }}, false)"
                                        class="bg-[#1A3A6B] text-white text-[12px] font-semibold px-3 py-1.5 rounded-md hover:bg-[#0f2a5e] transition-all whitespace-nowrap">
                                    Registrar reporte
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-[150px] h-[120px] bg-[#EEF4FF] rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[#93B4E0] ms-fill" style="font-size: 64px;">calendar_today</span>
                                    </div>
                                    <p class="font-semibold text-[#1A3A6B] text-base">Sin consultas en este período</p>
                                    <p class="text-gray-500 text-sm max-w-xs">
                                        Selecciona otro período o espera a que la gestora registre nuevas citas.
                                    </p>
                                    <a href="{{ route('medico.consultas') }}"
                                       class="border border-[#1A3A6B] text-[#1A3A6B] px-5 py-2 rounded-lg text-sm font-semibold hover:bg-[#EEF4FF] transition-all">
                                        Ver todos los períodos
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($consultas->hasPages())
                <div class="bg-[#F9FAFB] px-5 py-3 border-t border-[#E5E7EB] flex items-center justify-between flex-wrap gap-3">
                    <p class="text-[13px] text-gray-500">
                        Mostrando <strong>{{ $consultas->firstItem() }}</strong>–<strong>{{ $consultas->lastItem() }}</strong>
                        de <strong>{{ $consultas->total() }}</strong> consultas
                    </p>
                    <div class="flex items-center gap-1">
                        @if($consultas->onFirstPage())
                            <span class="page-btn disabled text-gray-400">
                                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            </span>
                        @else
                            <a href="{{ $consultas->previousPageUrl() }}" class="page-btn text-gray-500">
                                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            </a>
                        @endif

                        @foreach($consultas->getUrlRange(max(1,$consultas->currentPage()-2), min($consultas->lastPage(),$consultas->currentPage()+2)) as $page => $url)
                            <a href="{{ $url }}" class="page-btn {{ $page == $consultas->currentPage() ? 'active' : 'text-gray-500' }}">
                                {{ $page }}
                            </a>
                        @endforeach

                        @if($consultas->hasMorePages())
                            <a href="{{ $consultas->nextPageUrl() }}" class="page-btn text-gray-500">
                                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </a>
                        @else
                            <span class="page-btn disabled text-gray-400">
                                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </span>
                        @endif
                    </div>
                </div>
                @endif

            </div>{{-- /tabla card --}}
        </div>{{-- /max-w --}}
    </main>
</div>{{-- /main-wrapper --}}

{{-- ══════════════════════════════════ DRAWER ══════════════════════════════════════ --}}

{{-- Overlay --}}
<div id="drawer-overlay" onclick="cerrarDrawer()"></div>

{{-- Panel --}}
<div id="drawer-panel">

    {{-- Header drawer (azul) --}}
    <div class="bg-[#1A3A6B] px-6 py-5 flex items-center justify-between flex-shrink-0">
        <h3 id="drawer-titulo" class="font-semibold text-white text-[17px]">Registrar Reporte</h3>
        <button onclick="cerrarDrawer()"
                class="w-8 h-8 flex items-center justify-center rounded-full text-white hover:bg-white/20 transition-all"
                style="background: rgba(255,255,255,0.15);">
            <span class="material-symbols-outlined text-[16px]">close</span>
        </button>
    </div>

    {{-- Resumen paciente --}}
    <div class="flex-shrink-0 px-6 py-4 border-b border-[#E5E7EB]">
        <div class="bg-[#F0F4FF] rounded-lg px-4 py-3">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-[#1A3A6B] flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-white text-[18px] ms-fill">person</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p id="pac-nombre" class="font-semibold text-[#1A3A6B] text-sm leading-tight">—</p>
                    <p id="pac-info"   class="text-[12px] text-gray-500 mt-0.5">—</p>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-3 flex-wrap">
                <span id="pac-esp"  class="esp-badge esp-default text-[11px]">—</span>
                <span id="pac-tipo" class="bg-[#EEF4FF] text-[#1A3A6B] text-[11px] font-medium px-2 py-0.5 rounded-full">—</span>
                <span id="pac-fecha" class="flex items-center gap-1 text-[12px] text-gray-400">
                    <span class="material-symbols-outlined text-[13px]">calendar_month</span>
                    <span id="pac-fecha-val">—</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Cuerpo del formulario (scrollable) --}}
    <div class="flex-1 overflow-y-auto px-6 py-4" id="drawer-body">
        <form id="form-reporte">
            @csrf
            <input type="hidden" id="field-cit-id" name="cit_id" value="">

            {{-- Campo 1: Fecha (readonly) --}}
            <div class="mb-4">
                <label class="block text-[13px] font-medium text-gray-700 mb-1">Fecha de atención</label>
                <input type="date" id="field-fecha" name="fec_atencion"
                       class="field-input" disabled
                       title="La fecha se establece automáticamente según la cita registrada"/>
            </div>

            {{-- Campo 2: Diagnóstico --}}
            <div class="mb-4">
                <label class="block text-[13px] font-medium text-gray-700 mb-1">Diagnóstico / Observaciones</label>
                <div class="relative">
                    <textarea id="field-diagnostico" name="diagnostico_rep" rows="5"
                              maxlength="1000"
                              placeholder="Describe el diagnóstico, tratamiento indicado u observaciones relevantes de la consulta..."
                              oninput="updateCharCount(this)"
                              class="field-input resize-none"></textarea>
                    <span id="char-count" class="absolute bottom-2 right-3 text-[11px] text-gray-400">0/1000</span>
                </div>
            </div>

            {{-- Campo 3: Tipo de consulta --}}
            <div class="mb-4">
                <label class="block text-[13px] font-medium text-gray-700 mb-1">Tipo de consulta realizada</label>
                <select id="field-tipo" name="tipo_rep" class="field-input">
                    <option value="">Seleccionar tipo...</option>
                    <option value="Consulta">Consulta regular</option>
                    <option value="Reconsulta_0_7">Reconsulta 0–7 días</option>
                    <option value="Reconsulta_8_15">Reconsulta 8–15 días</option>
                    <option value="Campana">Campaña</option>
                </select>
            </div>

            {{-- Campo 4: ¿Se presentó? --}}
            <div class="mb-4 bg-[#F9FAFB] rounded-lg p-4 border border-[#E5E7EB]">
                <p class="font-semibold text-[#1A1A2E] text-[15px] mb-1">¿El paciente se presentó a la consulta?</p>
                <p class="text-[12px] text-gray-400 mb-3">Tu respuesta activa el ciclo de seguimiento del paciente.</p>
                <div class="flex gap-3">
                    <button type="button" id="btn-si" onclick="seleccionarAsistencia(true)"
                            class="asist-btn">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        Sí, se presentó
                    </button>
                    <button type="button" id="btn-no" onclick="seleccionarAsistencia(false)"
                            class="asist-btn">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                        No se presentó
                    </button>
                </div>
                <input type="hidden" id="field-asistio" name="paciente_asistio" value="">
            </div>

            {{-- Sección condicional: Motivo de ausencia --}}
            <div id="motivo-section" class="mb-4">
                <div class="flex items-center gap-2 my-3">
                    <hr class="flex-1 border-[#E5E7EB]"/>
                    <span class="text-[12px] text-gray-400 px-2">Motivo de ausencia</span>
                    <hr class="flex-1 border-[#E5E7EB]"/>
                </div>
                <label class="block text-[13px] font-medium text-gray-700 mb-1">¿Por qué no asistió el paciente?</label>
                <select id="field-motivo" name="motivo_no_asistio"
                        onchange="onMotivoChange(this)"
                        class="field-input mb-3">
                    <option value="">Seleccionar motivo...</option>
                    <option value="Cancelo_por_telefono">📞  Canceló por teléfono — el paciente avisó</option>
                    <option value="No_se_presento_sin_aviso">🚶  No se presentó sin aviso</option>
                    <option value="Derivado_consulta_privada">🏠  Derivado a consulta privada</option>
                    <option value="Otro">✏️  Otro motivo</option>
                </select>
                {{-- Alerta de derivación --}}
                <div id="alerta-derivacion"
                     class="hidden items-center gap-2 bg-[#FFF8E1] border border-[#FCD34D] rounded-lg px-3 py-2 mb-3"
                     style="border-left: 3px solid #F5A623;">
                    <span class="material-symbols-outlined text-[#F5A623] ms-fill text-[18px]">warning</span>
                    <span class="text-[12px] text-[#92400E] font-medium">Alerta de fuga — se notificará al Administrador</span>
                </div>
                {{-- Textarea para "Otro" --}}
                <div id="motivo-otro-div" class="hidden">
                    <textarea id="field-motivo-otro" name="motivo_otro_rep" rows="3"
                              placeholder="Describe el motivo..."
                              class="field-input resize-none"></textarea>
                </div>
            </div>

            {{-- Caja informativa --}}
            <div class="flex items-start gap-3 rounded-lg px-4 py-3 mb-4"
                 style="background:#FFFBEB; border: 1px solid #FCD34D; border-left: 4px solid #F5A623;">
                <span class="material-symbols-outlined text-[#F5A623] ms-fill text-[20px] flex-shrink-0 mt-0.5">info</span>
                <p class="text-[12px] text-[#92400E] leading-relaxed">
                    Si no registras este reporte antes del vencimiento del plazo, la consulta será marcada automáticamente
                    como <strong>Fuga Sospechosa</strong> y se notificará al Administrador.
                </p>
            </div>

        </form>
    </div>

    {{-- Footer drawer --}}
    <div class="flex-shrink-0 px-6 py-4 border-t border-[#E5E7EB] flex gap-3 bg-white">
        <button type="button" onclick="cerrarDrawer()"
                class="flex-none border border-[#D1D5DB] text-gray-600 font-semibold text-sm px-6 py-2.5 rounded-lg hover:bg-gray-50 transition-all"
                style="flex: 0 0 40%;">
            Cancelar
        </button>
        <button type="button" id="btn-guardar" onclick="guardarReporte()" disabled
                class="font-semibold text-white text-sm px-6 py-2.5 rounded-lg transition-all flex items-center justify-center gap-2 bg-[#1A3A6B] hover:bg-[#0f2a5e]"
                style="flex: 1;">
            <span id="btn-guardar-text">Guardar Reporte</span>
            <span id="btn-guardar-spinner" class="hidden">
                <span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
            </span>
        </button>
    </div>

</div>{{-- /drawer-panel --}}

{{-- ════════════════════════════════ JAVASCRIPT ════════════════════════════════════ --}}
<script>
    const CSRF   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let   citaActualId  = null;
    let   asistioActual = null;
    let   modoVer       = false;

    // ── Sidebar toggle ───────────────────────────────────────────────────────
    function toggleSidebar() {
        const sb   = document.getElementById('sidebar');
        const wrap = document.getElementById('main-wrapper');
        const icon = document.getElementById('toggle-icon');
        sb.classList.toggle('collapsed');
        wrap.classList.toggle('sidebar-collapsed');
        icon.textContent = sb.classList.contains('collapsed') ? 'menu' : 'menu_open';
    }

    // ── Abrir drawer ─────────────────────────────────────────────────────────
    async function abrirDrawer(idCit, soloVer) {
        citaActualId = idCit;
        modoVer      = soloVer;
        resetForm();

        try {
            const res  = await fetch(`/medico/consultas/${idCit}/reporte`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            if (!res.ok) throw new Error('Error al cargar datos');
            const data = await res.json();
            llenarDrawer(data, soloVer);
        } catch (e) {
            alert('No se pudo cargar la información de la consulta.');
            return;
        }

        document.getElementById('drawer-overlay').classList.add('open');
        document.getElementById('drawer-panel').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    // ── Cerrar drawer ────────────────────────────────────────────────────────
    function cerrarDrawer() {
        document.getElementById('drawer-overlay').classList.remove('open');
        document.getElementById('drawer-panel').classList.remove('open');
        document.body.style.overflow = '';
        resetForm();
    }

    // ── Resetear formulario ──────────────────────────────────────────────────
    function resetForm() {
        document.getElementById('form-reporte').reset();
        document.getElementById('field-asistio').value = '';
        document.getElementById('btn-si').className      = 'asist-btn';
        document.getElementById('btn-no').className      = 'asist-btn';
        document.getElementById('motivo-section').style.display = 'none';
        document.getElementById('alerta-derivacion').classList.add('hidden');
        document.getElementById('motivo-otro-div').classList.add('hidden');
        document.getElementById('btn-guardar').disabled = true;
        document.getElementById('char-count').textContent = '0/1000';
        asistioActual = null;
    }

    // ── Llenar drawer con datos del servidor ─────────────────────────────────
    function llenarDrawer(data, soloVer) {
        const { cita, paciente, especialidad, reporte_existente } = data;

        document.getElementById('drawer-titulo').textContent = soloVer ? 'Ver Reporte' : 'Registrar Reporte';
        document.getElementById('field-cit-id').value = cita.id_cit;
        document.getElementById('pac-nombre').textContent   = paciente.nombre || '—';
        document.getElementById('pac-info').textContent     = `CI: ${paciente.ci} · Tel: ${paciente.telefono || '—'}`;
        document.getElementById('pac-esp').textContent      = especialidad || '—';
        document.getElementById('pac-tipo').textContent     = formatTipo(cita.tipo_cit);
        document.getElementById('pac-fecha-val').textContent = cita.fec_display || '—';

        // Fecha atención
        const fechaField = document.getElementById('field-fecha');
        fechaField.value = cita.fec_cit;

        // Pre-llenar tipo
        document.getElementById('field-tipo').value = cita.tipo_cit || '';

        if (soloVer && reporte_existente) {
            // Modo sólo lectura
            const r = reporte_existente;
            document.getElementById('field-diagnostico').value = r.diagnostico_rep || '';
            document.getElementById('field-tipo').value        = r.tipo_rep || '';

            if (r.paciente_asistio !== null) {
                seleccionarAsistencia(r.paciente_asistio, true);
            }
            if (!r.paciente_asistio && r.motivo_no_asistio) {
                document.getElementById('field-motivo').value = r.motivo_no_asistio;
                onMotivoChange(document.getElementById('field-motivo'), true);
                if (r.motivo_otro_rep) {
                    document.getElementById('field-motivo-otro').value = r.motivo_otro_rep;
                }
            }

            // Bloquear todo en modo ver
            setFormReadOnly(true);
            document.getElementById('btn-guardar').style.display = 'none';
        } else {
            setFormReadOnly(false);
            document.getElementById('btn-guardar').style.display = '';
            document.getElementById('btn-guardar').disabled = true;
        }
    }

    function setFormReadOnly(ro) {
        const fields = document.querySelectorAll('#form-reporte input, #form-reporte select, #form-reporte textarea');
        fields.forEach(f => {
            if (f.id === 'field-fecha') return;
            f.disabled = ro;
        });
        document.getElementById('btn-si').disabled = ro;
        document.getElementById('btn-no').disabled = ro;
    }

    function formatTipo(tipo) {
        const map = {
            'Consulta': 'Consulta regular',
            'Reconsulta_0_7': 'Reconsulta 0–7d',
            'Reconsulta_8_15': 'Reconsulta 8–15d',
            'Campana': 'Campaña'
        };
        return map[tipo] || tipo;
    }

    // ── Selección de asistencia ──────────────────────────────────────────────
    function seleccionarAsistencia(si, soloEstilo) {
        asistioActual = si;
        document.getElementById('field-asistio').value = si ? '1' : '0';

        document.getElementById('btn-si').className = 'asist-btn' + (si ? ' si-active' : '');
        document.getElementById('btn-no').className = 'asist-btn' + (!si ? ' no-active' : '');

        const motivoSec = document.getElementById('motivo-section');
        if (!si) {
            motivoSec.style.display = 'block';
        } else {
            motivoSec.style.display = 'none';
            document.getElementById('field-motivo').value = '';
            document.getElementById('alerta-derivacion').classList.add('hidden');
            document.getElementById('motivo-otro-div').classList.add('hidden');
        }

        if (!soloEstilo) verificarFormulario();
    }

    // ── Cambio en select de motivo ───────────────────────────────────────────
    function onMotivoChange(sel, soloEstilo) {
        const val        = sel.value;
        const alertaDiv  = document.getElementById('alerta-derivacion');
        const otroDiv    = document.getElementById('motivo-otro-div');

        if (val === 'Derivado_consulta_privada') {
            alertaDiv.classList.remove('hidden');
            alertaDiv.classList.add('flex');
        } else {
            alertaDiv.classList.add('hidden');
            alertaDiv.classList.remove('flex');
        }

        if (val === 'Otro') {
            otroDiv.classList.remove('hidden');
        } else {
            otroDiv.classList.add('hidden');
            document.getElementById('field-motivo-otro').value = '';
        }

        if (!soloEstilo) verificarFormulario();
    }

    // ── Verificar si el formulario está listo para guardar ───────────────────
    function verificarFormulario() {
        const asistio = document.getElementById('field-asistio').value;
        if (asistio === '') {
            document.getElementById('btn-guardar').disabled = true;
            return;
        }
        if (asistio === '0') {
            const motivo = document.getElementById('field-motivo').value;
            if (!motivo) {
                document.getElementById('btn-guardar').disabled = true;
                return;
            }
            if (motivo === 'Otro' && !document.getElementById('field-motivo-otro').value.trim()) {
                document.getElementById('btn-guardar').disabled = true;
                return;
            }
        }
        document.getElementById('btn-guardar').disabled = false;
    }

    // Escuchar cambios en motivo-otro para re-verificar
    document.getElementById('field-motivo-otro')?.addEventListener('input', verificarFormulario);
    document.getElementById('field-motivo')?.addEventListener('change', () => verificarFormulario());

    // ── Guardar reporte ──────────────────────────────────────────────────────
    async function guardarReporte() {
        const idCit = citaActualId;
        if (!idCit) return;

        const btn    = document.getElementById('btn-guardar');
        const text   = document.getElementById('btn-guardar-text');
        const spinner = document.getElementById('btn-guardar-spinner');

        btn.disabled       = true;
        text.textContent   = 'Guardando...';
        spinner.classList.remove('hidden');

        const formData = new FormData();
        formData.append('_token',            CSRF);
        formData.append('diagnostico_rep',   document.getElementById('field-diagnostico').value);
        formData.append('tipo_rep',          document.getElementById('field-tipo').value);
        formData.append('paciente_asistio',  document.getElementById('field-asistio').value);
        const motivo = document.getElementById('field-motivo').value;
        if (motivo) formData.append('motivo_no_asistio', motivo);
        const otroText = document.getElementById('field-motivo-otro').value;
        if (otroText)  formData.append('motivo_otro_rep', otroText);

        try {
            const res = await fetch(`/medico/consultas/${idCit}/reporte`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: formData
            });

            if (res.status === 422) {
                const err = await res.json();
                const msgs = Object.values(err.errors || {}).flat().join('\n');
                alert('Errores de validación:\n' + msgs);
                btn.disabled = false;
                text.textContent = 'Guardar Reporte';
                spinner.classList.add('hidden');
                return;
            }

            if (!res.ok) throw new Error('Error del servidor');

            // Éxito: cerrar y recargar para reflejar cambios
            cerrarDrawer();
            window.location.reload();

        } catch (e) {
            alert('No se pudo guardar el reporte. Inténtalo de nuevo.');
            btn.disabled = false;
            text.textContent = 'Guardar Reporte';
            spinner.classList.add('hidden');
        }
    }

    // ── Contador de caracteres ────────────────────────────────────────────────
    function updateCharCount(el) {
        document.getElementById('char-count').textContent = el.value.length + '/1000';
        verificarFormulario();
    }

    // ── Cerrar con Escape ────────────────────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') cerrarDrawer();
    });
</script>
</body>
</html>
