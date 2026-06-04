<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Dashboard Ejecutivo — CRM Centro Médico</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
        .ms-fill { font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }

        /* ── Sidebar fija 220px ─────────────────────────── */
        #sidebar { width: 220px; flex-shrink: 0; }
        .nav-item-active {
            background: rgba(255,255,255,0.15);
            box-shadow: inset 3px 0 0 #F5A623;
            color: #fff !important;
        }

        /* ── KPI card ───────────────────────────────────── */
        .kpi-card { border-radius: 10px; padding: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }

        /* KPI fuga — card especial ─────────────────────── */
        .kpi-fuga {
            border: 2px solid #D94040 !important;
            box-shadow: 0 4px 12px rgba(217,64,64,0.2) !important;
        }

        /* ── Cards colapsables ──────────────────────────── */
        .card-body { overflow: hidden; transition: max-height 0.25s ease, opacity 0.2s ease; }
        .card-body.collapsed { max-height: 0 !important; opacity: 0; }
        .chevron-icon { transition: transform 0.2s ease; }
        .chevron-icon.rotated { transform: rotate(180deg); }

        /* ── Tabla base ─────────────────────────────────── */
        .dash-table thead tr th {
            background: #1A3A6B; color: #fff;
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px;
            padding: 10px 14px;
        }
        .dash-table tbody tr td { padding: 10px 14px; font-size: 13px; border-bottom: 1px solid #F3F4F6; }
        .dash-table tbody tr:nth-child(even) { background: #F9FAFB; }
        .dash-table tbody tr:hover { background: #EEF4FF; }

        /* ── Semáforo cancelación ───────────────────────── */
        .cancel-verde  { color: #2E7D32; font-weight: 600; }
        .cancel-naranja{ color: #F57F17; font-weight: 600; }
        .cancel-rojo   { color: #C62828; font-weight: 600; background: #FCE4EC; padding: 1px 6px; border-radius: 4px; }

        /* ── Badge pulsante (alertas) ───────────────────── */
        @keyframes pulse-badge {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.6; }
        }
        .pulse { animation: pulse-badge 1.5s ease-in-out infinite; }

        /* ── Barra de progreso export ───────────────────── */
        @keyframes progress-run { from { width:0 } to { width:100% } }
        .export-progress { animation: progress-run 1.8s ease-in-out forwards; }

        /* ── Barra horizontal chart ─────────────────────── */
        .bar-wrap { height: 24px; border-radius: 4px; overflow: hidden; }
    </style>
</head>
<body class="bg-[#F7F8FA] h-screen flex overflow-hidden">

{{-- ═══════════════════════ SIDEBAR 220px ═══════════════════════ --}}
<aside id="sidebar" class="fixed left-0 top-0 h-screen bg-[#1A3A6B] flex flex-col z-50 overflow-y-auto">

    {{-- Header sidebar --}}
    <div class="px-4 py-5" style="min-height:80px; border-bottom:1px solid rgba(255,255,255,0.15);">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-white ms-fill text-[18px]">local_hospital</span>
            </div>
            <div class="overflow-hidden">
                <p class="font-semibold text-white leading-tight" style="font-size:13px;">Centro Médico</p>
                <p class="text-white/60 leading-tight" style="font-size:11px;">Alemana Red de Salud</p>
            </div>
        </div>
    </div>

    {{-- Menú de navegación --}}
    <nav class="flex-1 p-2 space-y-0.5 mt-2">
        <a href="{{ route('admin.dashboard') }}"
           class="nav-item-active flex items-center gap-3 px-4 py-2.5 rounded-lg text-white transition-all text-[13px] font-medium">
            <span class="material-symbols-outlined text-[18px] ms-fill flex-shrink-0">dashboard</span>
            Dashboard
        </a>
        {{-- Los siguientes módulos están en el dashboard analítico — anclas a las cards --}}
        <a href="{{ route('admin.dashboard') }}#card-estado"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-all text-[13px] font-medium">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0">assignment</span>
            Consultas
        </a>
        <a href="{{ route('admin.dashboard') }}#card-esp"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-all text-[13px] font-medium">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0">stethoscope</span>
            Médicos
        </a>
        <a href="{{ route('admin.dashboard') }}#card-seguro"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-all text-[13px] font-medium">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0">group</span>
            Pacientes
        </a>
        <a href="{{ route('admin.dashboard') }}#card-esp"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-all text-[13px] font-medium">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0">star</span>
            Especialidades
        </a>
        <a href="{{ route('admin.dashboard') }}#card-alertas"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-all text-[13px] font-medium">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0">bar_chart</span>
            Reportes
        </a>

        <div class="border-t border-white/15 my-2"></div>

        <a href="{{ route('admin.dashboard') }}#card-export"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-all text-[13px] font-medium">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0">download</span>
            Exportar datos
        </a>
    </nav>

    {{-- Footer sidebar --}}
    <div class="p-4 border-t border-white/15">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-full bg-[#F5A623] flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr($usuario->nom_usu ?? 'A', 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-white font-medium leading-tight truncate" style="font-size:13px;">
                    {{ trim(($usuario->nom_usu ?? '') . ' ' . ($usuario->apat_usu ?? '')) ?: 'Admin' }}
                </p>
                <p class="text-white/60 leading-tight" style="font-size:11px;">Administrador</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 text-white/50 hover:text-white/80 text-[12px] transition-all">
                <span class="material-symbols-outlined text-[16px]">logout</span>
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>

{{-- ══════════════════════ MAIN WRAPPER ══════════════════════════ --}}
<div class="flex-1 flex flex-col h-screen overflow-hidden" style="margin-left:220px;">

    {{-- ─── HEADER PRINCIPAL 64px ─────────────────────────────── --}}
    <header class="sticky top-0 z-40 bg-white flex items-center px-6 gap-4"
            style="height:64px; border-bottom:1px solid #E5E7EB; box-shadow:0 1px 4px rgba(0,0,0,0.06);">

        <div class="flex-1 min-w-0">
            <h1 class="font-bold text-[#1A3A6B] leading-tight" style="font-size:22px;">Dashboard Ejecutivo</h1>
            <p class="text-gray-400 leading-tight" style="font-size:13px;">Centro Médico · Minería de Datos</p>
        </div>

        {{-- Badge fuente --}}
        <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-[12px] text-[#1A3A6B]"
             style="background:#F0F4FF; border-color:#C7D9F5;">
            <span class="material-symbols-outlined text-[14px] ms-fill">description</span>
            Fuente: Sistema ADM — ReporteCitasMedicas
        </div>

        {{-- Campana + avatar --}}
        <div class="flex items-center gap-3">
            <div class="relative">
                <button class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-[#1A3A6B] hover:bg-[#EEF4FF] rounded-lg transition-all">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                </button>
                @if($notifBadge > 0)
                <span class="absolute -top-1 -right-1 bg-[#D94040] text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center leading-none">
                    {{ min($notifBadge, 9) }}
                </span>
                @endif
            </div>
            <div class="w-9 h-9 rounded-full bg-[#1A3A6B] flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr($usuario->nom_usu ?? 'A', 0, 1)) }}
            </div>
        </div>
    </header>

    {{-- ─── SUBHEADER PERÍODO 48px ─────────────────────────────── --}}
    <div class="sticky top-[64px] z-30 flex items-center px-6 gap-4 flex-wrap"
         style="height:48px; background:#F0F4FF; border-bottom:1px solid #C7D9F5;">

        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[#1A3A6B] text-[16px] ms-fill">calendar_month</span>
            <span class="text-gray-500 text-[13px] font-medium">Período analizado:</span>
            <span id="badge-periodo"
                  class="bg-[#1A3A6B] text-white text-[12px] font-medium px-3 py-0.5 rounded-full">
                {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($fin)->format('d/m/Y') }}
            </span>
        </div>

        {{-- Filtros rápidos --}}
        <div class="flex items-center gap-2 ml-auto flex-wrap">
            <button onclick="filtrarPeriodo('todo')"
                    id="chip-todo"
                    class="period-chip text-[12px] font-medium px-3 py-1 rounded-full border transition-all bg-[#1A3A6B] text-white border-[#1A3A6B]">
                Todo el período
            </button>
            <button onclick="filtrarPeriodo('mes')"
                    id="chip-mes"
                    class="period-chip text-[12px] font-medium px-3 py-1 rounded-full border transition-all text-[#1A3A6B] border-[#1A3A6B] hover:bg-[#EEF4FF]">
                Este mes
            </button>
            <button onclick="filtrarPeriodo('3m')"
                    id="chip-3m"
                    class="period-chip text-[12px] font-medium px-3 py-1 rounded-full border transition-all text-[#1A3A6B] border-[#1A3A6B] hover:bg-[#EEF4FF]">
                Últimos 3 meses
            </button>
            <button onclick="mostrarPersonalizado()"
                    id="chip-custom"
                    class="period-chip text-[12px] font-medium px-3 py-1 rounded-full border transition-all text-[#1A3A6B] border-[#1A3A6B] hover:bg-[#EEF4FF]">
                Personalizado
            </button>

            {{-- Picker personalizado --}}
            <div id="custom-picker" class="hidden items-center gap-1">
                <input type="date" id="picker-inicio" value="{{ $inicio }}"
                       class="border border-[#C7D9F5] rounded-lg px-2 py-1 text-[12px] text-[#1A3A6B] bg-white outline-none focus:border-[#1A3A6B]"/>
                <span class="text-[#9CA3AF] text-[12px]">—</span>
                <input type="date" id="picker-fin" value="{{ $fin }}"
                       class="border border-[#C7D9F5] rounded-lg px-2 py-1 text-[12px] text-[#1A3A6B] bg-white outline-none focus:border-[#1A3A6B]"/>
                <button onclick="aplicarPersonalizado()"
                        class="bg-[#1A3A6B] text-white text-[12px] px-3 py-1 rounded-lg hover:bg-[#0f2a5e] transition-all">
                    Aplicar
                </button>
            </div>

            <button id="btn-refresh" onclick="recargarDatos()"
                    class="flex items-center gap-1.5 border border-[#1A3A6B] text-[#1A3A6B] text-[13px] px-3 py-1 rounded-lg hover:bg-[#EEF4FF] transition-all">
                <span class="material-symbols-outlined text-[15px]" id="refresh-icon">refresh</span>
                Actualizar
            </button>
        </div>
    </div>

    {{-- ══════════════════ CONTENIDO PRINCIPAL ═══════════════════ --}}
    <main class="flex-1 overflow-y-auto p-6 space-y-5" style="min-height:0;">

        {{-- ──────────────────────────────────────────────────────
             CARD 1 — KPIs (no colapsable)
        ────────────────────────────────────────────────────────── --}}
        <div id="card-kpis" class="bg-white rounded-xl" style="box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div class="px-6 py-5 text-center border-b border-[#F3F4F6]">
                <p class="text-[#F5A623] font-semibold uppercase tracking-widest mb-1" style="font-size:11px; letter-spacing:1px;">— RESUMEN EJECUTIVO —</p>
                <h2 class="font-semibold text-[#1A3A6B] text-base">Indicadores Clave del Período</h2>
                <p class="text-gray-400 text-[12px] mt-0.5">
                    {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($fin)->format('d/m/Y') }} · Centro Médico de Especialidades
                </p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-4 gap-3 mb-3" id="kpi-fila1">
                    {{-- K1: Total Citas --}}
                    <div class="kpi-card" style="border-top:4px solid #1A3A6B; background:#F0F4FF;">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-bold text-[#1A3A6B]" style="font-size:36px; line-height:1.1;" id="k-total">{{ $kpis['total_citas'] }}</p>
                                <p class="text-gray-500 text-[12px] mt-1">Total Citas Registradas</p>
                            </div>
                            <span class="material-symbols-outlined text-[#1A3A6B] text-[20px] mt-1 ms-fill">list_alt</span>
                        </div>
                    </div>
                    {{-- K2: Finalizadas --}}
                    <div class="kpi-card" style="border-top:4px solid #2E7D32; background:#F1F8E9;">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-bold text-[#2E7D32]" style="font-size:36px; line-height:1.1;" id="k-final">{{ $kpis['finalizadas'] }}</p>
                                <p class="text-gray-500 text-[12px] mt-1">Citas Finalizadas</p>
                                <p class="text-[#2E7D32] text-[11px] font-medium" id="k-final-pct">{{ $kpis['pct_finalizadas'] }}% del total</p>
                            </div>
                            <span class="material-symbols-outlined text-[#2E7D32] text-[20px] mt-1 ms-fill">check_circle</span>
                        </div>
                    </div>
                    {{-- K3: Canceladas --}}
                    <div class="kpi-card" style="border-top:4px solid #D94040; background:#FFEBEE;">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-bold text-[#D94040]" style="font-size:36px; line-height:1.1;" id="k-cancel">{{ $kpis['canceladas'] }}</p>
                                <p class="text-gray-500 text-[12px] mt-1">Citas Canceladas</p>
                                <p class="text-[#D94040] text-[11px] font-medium" id="k-cancel-pct">{{ $kpis['pct_canceladas'] }}% del total</p>
                            </div>
                            <span class="material-symbols-outlined text-[#D94040] text-[20px] mt-1 ms-fill">cancel</span>
                        </div>
                    </div>
                    {{-- K4: Pacientes únicos --}}
                    <div class="kpi-card" style="border-top:4px solid #1565C0; background:#E3F2FD;">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-bold text-[#1565C0]" style="font-size:36px; line-height:1.1;" id="k-pac">{{ $kpis['pacientes_unicos'] }}</p>
                                <p class="text-gray-500 text-[12px] mt-1">Pacientes Únicos</p>
                            </div>
                            <span class="material-symbols-outlined text-[#1565C0] text-[20px] mt-1 ms-fill">person</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-3" id="kpi-fila2">
                    {{-- K5: Tasa recurrencia --}}
                    <div class="kpi-card" style="border-top:4px solid #2E7D32; background:#F1F8E9;">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-bold text-[#2E7D32]" style="font-size:36px; line-height:1.1;" id="k-recur">{{ $kpis['tasa_recurrencia'] }}%</p>
                                <p class="text-gray-500 text-[12px] mt-1">Tasa de Recurrencia</p>
                                <p class="text-[#2E7D32] text-[11px]">Pacientes que volvieron</p>
                            </div>
                            <span class="material-symbols-outlined text-[#2E7D32] text-[20px] mt-1 ms-fill">autorenew</span>
                        </div>
                    </div>
                    {{-- K6: Tasa fuga — DESTACADA --}}
                    <div class="kpi-card kpi-fuga" style="border-top:4px solid #D94040; background:#FFEBEE;">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-bold text-[#D94040]" style="font-size:40px; line-height:1.1;" id="k-fuga">{{ $kpis['tasa_fuga_potencial'] }}%</p>
                                <p class="text-gray-500 text-[12px] mt-1">Tasa de Fuga Potencial</p>
                                <p class="text-[#D94040] text-[11px] font-medium" id="k-fuga-count">{{ $kpis['pacientes_fuga_count'] }} pacientes no retornaron</p>
                            </div>
                            <span class="material-symbols-outlined text-[#D94040] text-[20px] mt-1 ms-fill">exit_to_app</span>
                        </div>
                    </div>
                    {{-- K7: Tasa cancelación --}}
                    <div class="kpi-card" style="border-top:4px solid #F5A623; background:#FFF8E1;">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-bold text-[#F5A623]" style="font-size:36px; line-height:1.1;" id="k-tasacancel">{{ $kpis['tasa_cancelacion'] }}%</p>
                                <p class="text-gray-500 text-[12px] mt-1">Tasa de Cancelación</p>
                                <p class="text-[#F5A623] text-[11px]" id="k-tasacancel-count">{{ $kpis['canceladas'] }} citas canceladas</p>
                            </div>
                            <span class="material-symbols-outlined text-[#F5A623] text-[20px] mt-1 ms-fill">event_busy</span>
                        </div>
                    </div>
                    {{-- K8: Especialidades activas --}}
                    <div class="kpi-card" style="border-top:4px solid #6A1B9A; background:#F3E5F5;">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-bold text-[#6A1B9A]" style="font-size:36px; line-height:1.1;" id="k-esp">{{ $kpis['especialidades_activas'] }}</p>
                                <p class="text-gray-500 text-[12px] mt-1">Especialidades Activas</p>
                                <p class="text-[#6A1B9A] text-[11px]">en el período</p>
                            </div>
                            <span class="material-symbols-outlined text-[#6A1B9A] text-[20px] mt-1 ms-fill">medical_services</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ──────────────────────────────────────────────────────
             CARD 2 — Estado de Citas
        ────────────────────────────────────────────────────────── --}}
        <div id="card-estado" class="bg-white rounded-xl overflow-hidden" style="box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            {{-- Header colapsable --}}
            <button onclick="toggleCard('estado')" class="w-full flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-all text-left">
                <div class="w-9 h-9 rounded-full bg-[#EEF4FF] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#1A3A6B] text-[18px] ms-fill">donut_large</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-[#1A3A6B] text-[15px] leading-tight">Distribución de Estados de Citas</p>
                    <p class="text-gray-400 text-[12px]">Desglose del total de {{ $estados['total'] }} citas por estado</p>
                </div>
                <span class="bg-[#1A3A6B] text-white text-[11px] font-medium px-3 py-1 rounded-full whitespace-nowrap">
                    {{ $estados['total'] }} citas · {{ count($estados['estados']) }} estados
                </span>
                <span class="material-symbols-outlined text-gray-400 text-[20px] chevron-icon ml-2" id="chv-estado">expand_more</span>
            </button>
            <div class="card-body" id="body-estado" style="max-height:500px;">
                <div class="grid grid-cols-2 gap-6 p-5 pt-0 border-t border-[#F3F4F6]" style="grid-template-columns:55% 45%;">
                    {{-- Tabla --}}
                    <div>
                        <table class="dash-table w-full rounded-lg overflow-hidden">
                            <thead><tr>
                                <th>Estado</th><th class="text-right">Cantidad</th><th class="text-right">% del Total</th>
                            </tr></thead>
                            <tbody>
                            @foreach($estados['estados'] as $e)
                            @php
                                $borde = match($e['estado']) {
                                    'Finalizado' => '#2E7D32', 'Cancelado' => '#D94040',
                                    'Reservado'  => '#1565C0', default       => '#F5A623',
                                };
                            @endphp
                            <tr style="border-left:4px solid {{ $borde }};">
                                <td class="font-medium">{{ $e['estado'] }}</td>
                                <td class="text-right font-bold">{{ number_format($e['cantidad']) }}</td>
                                <td class="text-right font-semibold" style="color:{{ $borde }};">{{ $e['porcentaje'] }}%</td>
                            </tr>
                            @endforeach
                            <tr style="background:#F0F4FF; font-weight:700; color:#1A3A6B;">
                                <td>TOTAL</td>
                                <td class="text-right">{{ number_format($estados['total']) }}</td>
                                <td class="text-right">100.0%</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- Gráfico dona --}}
                    <div class="flex flex-col items-center justify-center gap-4">
                        <div style="position:relative; width:220px; height:220px;">
                            <canvas id="chart-dona"></canvas>
                            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; pointer-events:none;">
                                <p class="font-bold text-[#1A3A6B]" style="font-size:28px; line-height:1;">{{ $estados['total'] }}</p>
                                <p class="text-gray-400 text-[12px]">Total citas</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 justify-center">
                            @foreach($estados['estados'] as $e)
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $e['color'] }};"></span>
                                <span class="text-[12px] text-gray-500">{{ $e['estado'] }}</span>
                                <span class="text-[12px] font-bold" style="color:{{ $e['color'] }};">{{ $e['porcentaje'] }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ──────────────────────────────────────────────────────
             CARD 3 — Top 10 Especialidades
        ────────────────────────────────────────────────────────── --}}
        <div id="card-esp" class="bg-white rounded-xl overflow-hidden" style="box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <button onclick="toggleCard('esp')" class="w-full flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-all text-left">
                <div class="w-9 h-9 rounded-full bg-[#FFF8E1] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#F5A623] text-[18px] ms-fill">star</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-[#1A3A6B] text-[15px] leading-tight">Top 10 Especialidades</p>
                    <p class="text-gray-400 text-[12px]">Centro Médico · Ranking por volumen de citas</p>
                </div>
                @if(isset($especialidades['lider']))
                <span class="bg-[#F5A623] text-white text-[11px] font-medium px-3 py-1 rounded-full whitespace-nowrap">
                    {{ $especialidades['lider']['especialidad'] }} lidera con {{ $especialidades['lider']['total_citas'] }}
                </span>
                @endif
                <span class="material-symbols-outlined text-gray-400 text-[20px] chevron-icon ml-2" id="chv-esp">expand_more</span>
            </button>
            <div class="card-body" id="body-esp" style="max-height:600px;">
                <div class="grid gap-6 p-5 pt-0 border-t border-[#F3F4F6]" style="grid-template-columns:1fr 1fr;">
                    {{-- Tabla ranking --}}
                    <div>
                        <table class="dash-table w-full rounded-lg overflow-hidden">
                            <thead><tr>
                                <th>#</th><th>Especialidad</th><th class="text-right">Citas</th>
                                <th class="text-right">Pac. Únicos</th><th class="text-right">Cancel%</th>
                            </tr></thead>
                            <tbody>
                            @foreach($especialidades['especialidades'] as $e)
                            @php
                                $cancelClass = match($e['color_cancelacion']) {
                                    'rojo'    => 'cancel-rojo',
                                    'naranja' => 'cancel-naranja',
                                    default   => 'cancel-verde',
                                };
                                $rowStyle = $e['es_lider']
                                    ? 'background:#FFF8E1; border-left:4px solid #F5A623;'
                                    : '';
                            @endphp
                            <tr style="{{ $rowStyle }}">
                                <td class="text-gray-400 font-medium">{{ $e['posicion'] }}</td>
                                <td class="font-medium">{{ $e['especialidad'] }}</td>
                                <td class="text-right font-bold text-[#1A3A6B]">{{ $e['total_citas'] }}</td>
                                <td class="text-right text-gray-500">{{ $e['pacientes_unicos'] }}</td>
                                <td class="text-right"><span class="{{ $cancelClass }}">{{ $e['pct_cancelacion'] }}%</span></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Barras horizontales --}}
                    <div class="space-y-2.5 py-1">
                        @foreach($especialidades['especialidades'] as $e)
                        @php
                            $color = $e['es_lider'] ? '#F5A623' : '#1A3A6B';
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="text-[12px] text-gray-500 w-32 truncate flex-shrink-0">{{ $e['especialidad'] }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full" style="height:20px;">
                                <div class="h-full rounded-full flex items-center justify-end pr-2"
                                     style="width:{{ $e['pct_barra'] }}%; background:{{ $color }}; transition:width .4s ease;">
                                </div>
                            </div>
                            <span class="text-[12px] font-bold text-[#1A3A6B] w-8 text-right flex-shrink-0">{{ $e['total_citas'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ──────────────────────────────────────────────────────
             CARD 4 — Distribución por Seguro
        ────────────────────────────────────────────────────────── --}}
        <div id="card-seguro" class="bg-white rounded-xl overflow-hidden" style="box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <button onclick="toggleCard('seguro')" class="w-full flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-all text-left">
                <div class="w-9 h-9 rounded-full bg-[#E8F5E9] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#2E7D32] text-[18px] ms-fill">shield</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-[#1A3A6B] text-[15px] leading-tight">Distribución por Seguro Médico</p>
                    <p class="text-gray-400 text-[12px]">Origen de las consultas por tipo de cobertura</p>
                </div>
                @if(count($seguros['seguros']) > 0)
                <span class="bg-[#2E7D32] text-white text-[11px] font-medium px-3 py-1 rounded-full whitespace-nowrap">
                    {{ $seguros['seguros'][0]['seguro'] }} lidera · {{ $seguros['seguros'][0]['total'] }} citas
                </span>
                @endif
                <span class="material-symbols-outlined text-gray-400 text-[20px] chevron-icon ml-2" id="chv-seguro">expand_more</span>
            </button>
            <div class="card-body" id="body-seguro" style="max-height:600px;">
                <div class="grid gap-6 p-5 pt-0 border-t border-[#F3F4F6]" style="grid-template-columns:60% 40%;">
                    {{-- Tabla --}}
                    <div class="overflow-x-auto">
                        <table class="dash-table w-full rounded-lg overflow-hidden">
                            <thead><tr>
                                <th>Seguro</th><th class="text-right">Total</th>
                                <th class="text-right">Final.</th><th class="text-right">Cancel.</th>
                                <th class="text-right">Cancel%</th>
                            </tr></thead>
                            <tbody>
                            @foreach($seguros['seguros'] as $s)
                            @php
                                $cancelClass = match($s['color_cancelacion']) {
                                    'rojo'    => 'cancel-rojo',
                                    'naranja' => 'cancel-naranja',
                                    default   => 'cancel-verde',
                                };
                                $rowStyle = $s['es_lider']
                                    ? 'border-left:4px solid #2E7D32;'
                                    : '';
                            @endphp
                            <tr style="{{ $rowStyle }}">
                                <td class="font-medium text-[12px]">{{ $s['seguro'] }}</td>
                                <td class="text-right font-bold text-[#1A3A6B]">{{ $s['total'] }}</td>
                                <td class="text-right text-[#2E7D32]">{{ $s['finalizadas'] }}</td>
                                <td class="text-right text-[#D94040]">{{ $s['canceladas'] }}</td>
                                <td class="text-right"><span class="{{ $cancelClass }}">{{ $s['pct_cancelacion'] }}%</span></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Gráfico barras apiladas --}}
                    <div class="flex items-center justify-center" style="min-height:240px;">
                        <canvas id="chart-seguros" style="max-height:280px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ──────────────────────────────────────────────────────
             CARD 5 — Alertas de Fuga
        ────────────────────────────────────────────────────────── --}}
        <div id="card-alertas" class="bg-white rounded-xl overflow-hidden" style="box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <button onclick="toggleCard('alertas')" class="w-full flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-all text-left">
                <div class="w-9 h-9 rounded-full bg-[#FFEBEE] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#D94040] text-[18px] ms-fill">notifications_active</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-[#1A3A6B] text-[15px] leading-tight">Alertas de Fuga Activas</p>
                    <p class="text-gray-400 text-[12px]">Seguimientos sin reporte escalados al administrador</p>
                </div>
                <span class="pulse bg-[#D94040] text-white text-[11px] font-medium px-3 py-1 rounded-full whitespace-nowrap flex items-center gap-1">
                    <span>●</span> {{ $alertas['total'] }} alerta{{ $alertas['total'] !== 1 ? 's' : '' }} activa{{ $alertas['total'] !== 1 ? 's' : '' }}
                </span>
                <span class="material-symbols-outlined text-gray-400 text-[20px] chevron-icon ml-2" id="chv-alertas">expand_more</span>
            </button>
            <div class="card-body" id="body-alertas" style="max-height:800px;">
                <div class="p-5 pt-0 border-t border-[#F3F4F6] space-y-3">
                    @forelse($alertas['alertas'] as $al)
                    @php
                        $urgClass = match($al['urgencia']) {
                            'critica' => 'bg-[#D94040] text-white',
                            'alta'    => 'bg-[#F5A623] text-white',
                            default   => 'bg-[#FFF8E1] text-[#F57F17]',
                        };
                    @endphp
                    <div class="flex items-center gap-4 rounded-lg px-4 py-3.5"
                         style="background:#FFF5F5; border-left:4px solid #D94040;">
                        <span class="material-symbols-outlined text-[#D94040] ms-fill text-[22px] flex-shrink-0">warning</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-[#1A1A2E] text-sm leading-tight">{{ $al['paciente'] }}</p>
                            <p class="text-gray-500 text-[12px] mt-0.5">
                                Sin reporte del Dr. {{ $al['medico'] }}
                            </p>
                            <span class="inline-block mt-1 text-[11px] font-medium px-2 py-0.5 rounded-full"
                                  style="background:#EDE7F6; color:#4527A0;">{{ $al['especialidad'] }}</span>
                        </div>
                        <div class="text-right flex-shrink-0 space-y-1">
                            <p class="text-gray-400 text-[12px]">{{ $al['fecha_consulta'] }}</p>
                            <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-full {{ $urgClass }}">
                                Hace {{ $al['dias_sin_respuesta'] }} día{{ $al['dias_sin_respuesta'] !== 1 ? 's' : '' }}
                            </span>
                            <br>
                            <button class="mt-1 border border-[#D94040] text-[#D94040] text-[12px] px-3 py-1 rounded-lg hover:bg-[#FFEBEE] transition-all">
                                Revisar →
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center py-10 gap-3">
                        <span class="material-symbols-outlined text-[#2E7D32] ms-fill" style="font-size:56px;">check_circle</span>
                        <p class="font-semibold text-[#2E7D32] text-base">Sin alertas activas</p>
                        <p class="text-gray-500 text-[13px]">Todos los médicos están al día con sus reportes</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ──────────────────────────────────────────────────────
             CARD 6 — Exportación
        ────────────────────────────────────────────────────────── --}}
        <div id="card-export" class="bg-white rounded-xl overflow-hidden" style="box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <button onclick="toggleCard('export')" class="w-full flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-all text-left">
                <div class="w-9 h-9 rounded-full bg-[#E8F5E9] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#217346] text-[18px] ms-fill">download</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-[#1A3A6B] text-[15px] leading-tight">Exportar Reporte</p>
                    <p class="text-gray-400 text-[12px]">Genera el backup completo del período en Excel</p>
                </div>
                <span class="text-white text-[11px] font-medium px-3 py-1 rounded-full" style="background:#217346;">
                    Formato .xlsx
                </span>
                <span class="material-symbols-outlined text-gray-400 text-[20px] chevron-icon ml-2" id="chv-export">expand_more</span>
            </button>
            <div class="card-body" id="body-export" style="max-height:500px;">
                <div class="flex gap-0 p-5 pt-0 border-t border-[#F3F4F6]">
                    {{-- Configuración --}}
                    <div style="flex:0 0 60%; padding-right:24px; border-right:1px solid #F3F4F6;">
                        <p class="font-semibold text-[#1A3A6B] text-[14px] mb-4">Configurar exportación</p>
                        <div class="space-y-2.5" id="export-checks">
                            @foreach(['kpis'=>'KPIs y resumen ejecutivo','estados'=>'Distribución de estados','especialidades'=>'Top especialidades','seguros'=>'Distribución por seguro','alertas'=>'Alertas de fuga'] as $val=>$label)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="secciones[]" value="{{ $val }}"
                                       checked
                                       class="w-4 h-4 rounded border-gray-300 text-[#1A3A6B] focus:ring-[#1A3A6B]"/>
                                <span class="text-[13px] text-gray-700 group-hover:text-[#1A3A6B] transition-colors">{{ $label }}</span>
                            </label>
                            @endforeach
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="secciones[]" value="raw" checked
                                       class="w-4 h-4 rounded border-gray-300 text-[#1A3A6B] focus:ring-[#1A3A6B]"/>
                                <span class="text-[13px] text-gray-700 group-hover:text-[#1A3A6B] transition-colors">Listado completo de citas</span>
                            </label>
                        </div>
                    </div>
                    {{-- Preview --}}
                    <div style="flex:0 0 40%; padding-left:24px;" class="flex flex-col items-center justify-center gap-3">
                        <div class="w-full rounded-lg p-5 flex flex-col items-center gap-2 border border-dashed border-[#D1D5DB]"
                             style="background:#F9FAFB;">
                            <div class="w-12 h-12 rounded-lg bg-[#217346] flex items-center justify-center">
                                <span class="material-symbols-outlined text-white ms-fill text-[28px]">table_chart</span>
                            </div>
                            <p class="font-semibold text-[#1A3A6B] text-[14px]">ReporteCitasMedicas</p>
                            <p class="text-gray-400 text-[12px]">Centro Médico · {{ \Carbon\Carbon::parse($inicio)->locale('es')->isoFormat('MMMM YYYY') }}</p>
                            <p class="text-gray-400 text-[11px]">~245 KB estimado</p>
                        </div>
                        <button id="btn-export" onclick="descargarExcel()"
                                class="w-full flex items-center justify-center gap-2 text-white font-semibold text-[14px] py-3 rounded-lg transition-all"
                                style="background:#217346;">
                            <span class="material-symbols-outlined text-[18px] ms-fill" id="export-icon">download</span>
                            <span id="export-text">Exportar a Excel</span>
                        </button>
                        {{-- Barra de progreso --}}
                        <div id="export-progress-wrap" class="hidden w-full bg-gray-200 rounded-full" style="height:4px;">
                            <div class="export-progress h-full rounded-full" style="background:#217346;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>{{-- /main wrapper --}}

{{-- ═══════════════════════ JAVASCRIPT ═══════════════════════════ --}}
<script>
const CSRF  = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
// Fechas reales del rango completo de datos
const PERIODO_COMPLETO = {
    inicio: '{{ $inicio }}',
    fin:    '{{ $fin }}'
};
let periodoActual = { ...PERIODO_COMPLETO };

// ── Chart.js: Dona de estados ────────────────────────────────────
const donaData   = @json($estados['chart']);
const donaCtx    = document.getElementById('chart-dona').getContext('2d');
const donaChart  = new Chart(donaCtx, {
    type: 'doughnut',
    data: {
        labels: donaData.labels,
        datasets: [{
            data:            donaData.data,
            backgroundColor: donaData.colors,
            borderWidth:     3,
            borderColor:     '#fff',
            hoverOffset:     8,
        }]
    },
    options: {
        cutout: '68%',
        plugins: { legend: { display: false }, tooltip: {
            callbacks: {
                label: ctx => ` ${ctx.label}: ${ctx.raw} citas (${donaData.porcentajes[ctx.dataIndex]}%)`
            }
        }},
        animation: { animateRotate: true, duration: 600 }
    }
});

// ── Chart.js: Barras apiladas — seguros ─────────────────────────
const segData  = @json($seguros['chart']);
const segCtx   = document.getElementById('chart-seguros').getContext('2d');
const segChart = new Chart(segCtx, {
    type: 'bar',
    data: {
        labels: segData.labels,
        datasets: [
            {
                label: 'Finalizadas',
                data:  segData.finalizadas,
                backgroundColor: '#2E7D32',
                borderRadius: { topLeft:0, topRight:0, bottomLeft:4, bottomRight:4 },
                stack: 'stack'
            },
            {
                label: 'Canceladas',
                data:  segData.canceladas,
                backgroundColor: '#D94040',
                borderRadius: { topLeft:4, topRight:4, bottomLeft:0, bottomRight:0 },
                stack: 'stack'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 11 }, padding: 16 } },
            tooltip: { mode: 'index' }
        },
        scales: {
            x: {
                ticks: {
                    font: { family: 'Poppins', size: 11 },
                    maxRotation: 15
                },
                grid: { display: false }
            },
            y: {
                beginAtZero: true,
                ticks: { font: { family: 'Poppins', size: 11 } },
                grid: { color: '#F3F4F6' }
            }
        },
        animation: { duration: 600 }
    }
});

// ── Cards colapsables ────────────────────────────────────────────
function toggleCard(id) {
    const body = document.getElementById('body-' + id);
    const chv  = document.getElementById('chv-' + id);
    body.classList.toggle('collapsed');
    chv.classList.toggle('rotated');
}

// ── Filtros de período ───────────────────────────────────────────
function filtrarPeriodo(tipo) {
    const hoy  = new Date();
    const fmt  = d => d.toISOString().split('T')[0];
    let inicio, fin;

    if (tipo === 'todo') {
        // Mostrar TODOS los datos (rango real de la BD)
        periodoActual = { ...PERIODO_COMPLETO };
        setChipActivo('todo');
        actualizarBadgePeriodo();
        recargarDatos();
        return;
    } else if (tipo === 'mes') {
        inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        fin    = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
    } else if (tipo === '3m') {
        fin    = new Date(hoy);
        inicio = new Date(hoy);
        inicio.setMonth(hoy.getMonth() - 3);
    }

    periodoActual = { inicio: fmt(inicio), fin: fmt(fin) };
    setChipActivo(tipo);
    actualizarBadgePeriodo();
    recargarDatos();
}

function mostrarPersonalizado() {
    const div = document.getElementById('custom-picker');
    div.classList.toggle('hidden');
    div.classList.toggle('flex');
    setChipActivo('custom');
}

function aplicarPersonalizado() {
    periodoActual.inicio = document.getElementById('picker-inicio').value;
    periodoActual.fin    = document.getElementById('picker-fin').value;
    actualizarBadgePeriodo();
    recargarDatos();
    document.getElementById('custom-picker').classList.add('hidden');
    document.getElementById('custom-picker').classList.remove('flex');
}

function setChipActivo(tipo) {
    ['todo','mes','3m','custom'].forEach(t => {
        const el = document.getElementById('chip-' + t);
        if (!el) return;
        if (t === tipo) {
            el.classList.add('bg-[#1A3A6B]', 'text-white', 'border-[#1A3A6B]');
            el.classList.remove('text-[#1A3A6B]', 'hover:bg-[#EEF4FF]');
        } else {
            el.classList.remove('bg-[#1A3A6B]', 'text-white');
            el.classList.add('text-[#1A3A6B]', 'hover:bg-[#EEF4FF]');
        }
    });
}

function actualizarBadgePeriodo() {
    const fmt = iso => iso.split('-').reverse().join('/');
    document.getElementById('badge-periodo').textContent =
        fmt(periodoActual.inicio) + ' — ' + fmt(periodoActual.fin);
}

// ── Recargar datos desde la API ──────────────────────────────────
async function recargarDatos() {
    const icon = document.getElementById('refresh-icon');
    icon.classList.add('animate-spin');

    try {
        const url = `/admin/dashboard/data?inicio=${periodoActual.inicio}&fin=${periodoActual.fin}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
        if (!res.ok) throw new Error('Error de red');
        const d = await res.json();

        // Actualizar KPIs
        const k = d.kpis;
        document.getElementById('k-total').textContent         = k.total_citas;
        document.getElementById('k-final').textContent         = k.finalizadas;
        document.getElementById('k-final-pct').textContent     = k.pct_finalizadas + '% del total';
        document.getElementById('k-cancel').textContent        = k.canceladas;
        document.getElementById('k-cancel-pct').textContent    = k.pct_canceladas + '% del total';
        document.getElementById('k-pac').textContent           = k.pacientes_unicos;
        document.getElementById('k-recur').textContent         = k.tasa_recurrencia + '%';
        document.getElementById('k-fuga').textContent          = k.tasa_fuga_potencial + '%';
        document.getElementById('k-fuga-count').textContent    = k.pacientes_fuga_count + ' pacientes no retornaron';
        document.getElementById('k-tasacancel').textContent    = k.tasa_cancelacion + '%';
        document.getElementById('k-tasacancel-count').textContent = k.canceladas + ' citas canceladas';
        document.getElementById('k-esp').textContent           = k.especialidades_activas;

        // Actualizar gráfico dona
        const ec = d.estados.chart;
        donaChart.data.labels   = ec.labels;
        donaChart.data.datasets[0].data            = ec.data;
        donaChart.data.datasets[0].backgroundColor = ec.colors;
        donaChart.update('active');

        // Actualizar gráfico seguros
        const sc = d.seguros.chart;
        segChart.data.labels                    = sc.labels;
        segChart.data.datasets[0].data          = sc.finalizadas;
        segChart.data.datasets[1].data          = sc.canceladas;
        segChart.update('active');

    } catch (e) {
        console.error('Error al recargar:', e);
    } finally {
        icon.classList.remove('animate-spin');
    }
}

// ── Exportar Excel ───────────────────────────────────────────────
function descargarExcel() {
    const btn      = document.getElementById('btn-export');
    const icon     = document.getElementById('export-icon');
    const text     = document.getElementById('export-text');
    const progress = document.getElementById('export-progress-wrap');

    // Obtener secciones marcadas
    const checks   = Array.from(document.querySelectorAll('#export-checks input:checked'))
                         .map(el => el.value);

    btn.disabled   = true;
    btn.style.background = '#1A5C38';
    icon.textContent = 'hourglass_top';
    text.textContent = 'Generando archivo...';
    progress.classList.remove('hidden');

    // Construir URL
    const params = new URLSearchParams({
        inicio: periodoActual.inicio,
        fin:    periodoActual.fin
    });
    checks.forEach(s => params.append('secciones[]', s));

    const url = '/admin/dashboard/exportar?' + params.toString();

    // Trigger descarga via iframe para no bloquear la página
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);

    setTimeout(() => {
        btn.disabled = false;
        btn.style.background = '#217346';
        icon.textContent = 'download';
        text.textContent = 'Exportar a Excel';
        progress.classList.add('hidden');
        document.body.removeChild(iframe);
    }, 2500);
}
</script>
</body>
</html>
