<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReporteCitasExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DashboardController extends Controller
{
    // ─── Período por defecto: rango completo de todos los datos ─────────────
    private function getPeriodo(Request $request): array
    {
        if ($request->filled('inicio') && $request->filled('fin')) {
            $inicio = Carbon::parse($request->input('inicio'))->startOfDay();
            $fin    = Carbon::parse($request->input('fin'))->endOfDay();
        } else {
            // Demo: mostrar TODOS los datos disponibles en la BD
            $rango  = DB::selectOne('SELECT MIN(fec_cit) AS min_f, MAX(fec_cit) AS max_f FROM cita');
            $inicio = Carbon::parse($rango->min_f)->startOfDay();
            $fin    = Carbon::parse($rango->max_f)->endOfDay();
        }

        // Garantizar que inicio <= fin
        if ($inicio->gt($fin)) [$inicio, $fin] = [$fin, $inicio];

        return [$inicio->toDateString(), $fin->toDateString()];
    }

    // ─── GET /admin/dashboard ────────────────────────────────────────────────
    public function index(Request $request)
    {
        $usuario   = Auth::user();
        [$inicio, $fin] = $this->getPeriodo($request);

        // Cargar datos iniciales server-side para evitar flash en primer render
        $kpis           = $this->calcKpis($inicio, $fin);
        $estados        = $this->calcEstados($inicio, $fin);
        $especialidades = $this->calcEspecialidades($inicio, $fin);
        $seguros        = $this->calcSeguros($inicio, $fin);
        $alertas        = $this->calcAlertas();

        // Badge notificaciones (alertas sin leer del admin)
        $notifBadge = DB::table('notificacion')
            ->where('id_usu', $usuario->id_usu)
            ->where('leido_not', false)
            ->count();

        return view('admin.dashboard', compact(
            'usuario', 'kpis', 'estados', 'especialidades',
            'seguros', 'alertas', 'notifBadge', 'inicio', 'fin'
        ));
    }

    // ─── GET /admin/dashboard/data (API unificada) ───────────────────────────
    public function getData(Request $request): JsonResponse
    {
        [$inicio, $fin] = $this->getPeriodo($request);

        return response()->json([
            'kpis'           => $this->calcKpis($inicio, $fin),
            'estados'        => $this->calcEstados($inicio, $fin),
            'especialidades' => $this->calcEspecialidades($inicio, $fin),
            'seguros'        => $this->calcSeguros($inicio, $fin),
            'alertas'        => $this->calcAlertas(),
            'periodo'        => ['inicio' => $inicio, 'fin' => $fin],
            'generado_en'    => now()->format('d/m/Y H:i:s'),
        ]);
    }

    // ─── GET /admin/dashboard/kpis ───────────────────────────────────────────
    public function getKpis(Request $request): JsonResponse
    {
        [$inicio, $fin] = $this->getPeriodo($request);
        return response()->json($this->calcKpis($inicio, $fin));
    }

    // ─── GET /admin/dashboard/especialidades ─────────────────────────────────
    public function getTopEspecialidades(Request $request): JsonResponse
    {
        [$inicio, $fin] = $this->getPeriodo($request);
        return response()->json($this->calcEspecialidades($inicio, $fin));
    }

    // ─── GET /admin/dashboard/seguros ────────────────────────────────────────
    public function getSeguros(Request $request): JsonResponse
    {
        [$inicio, $fin] = $this->getPeriodo($request);
        return response()->json($this->calcSeguros($inicio, $fin));
    }

    // ─── GET /admin/dashboard/alertas ────────────────────────────────────────
    public function getAlertas(): JsonResponse
    {
        return response()->json($this->calcAlertas());
    }

    // ─── GET /admin/dashboard/exportar ───────────────────────────────────────
    public function exportar(Request $request): BinaryFileResponse
    {
        [$inicio, $fin] = $this->getPeriodo($request);

        $secciones = $request->input('secciones', [
            'kpis', 'estados', 'especialidades', 'seguros', 'alertas'
        ]);

        $nombre = 'ReporteCitasMedicas_'
            . Carbon::parse($inicio)->format('d-m-Y')
            . '_al_'
            . Carbon::parse($fin)->format('d-m-Y')
            . '.xlsx';

        return Excel::download(
            new ReporteCitasExport($inicio, $fin, $secciones),
            $nombre
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // MÉTODOS PRIVADOS DE CÁLCULO
    // ════════════════════════════════════════════════════════════════════════

    private function calcKpis(string $inicio, string $fin): array
    {
        $row = DB::selectOne("
            SELECT
                COUNT(c.id_cit)                                              AS total_citas,
                COUNT(CASE WHEN c.estado_cit = 'Finalizado'  THEN 1 END)     AS finalizadas,
                COUNT(CASE WHEN c.estado_cit = 'Cancelado'   THEN 1 END)     AS canceladas,
                COUNT(CASE WHEN c.estado_cit = 'Reservado'   THEN 1 END)     AS reservadas,
                COUNT(CASE WHEN c.estado_cit = 'Triage'      THEN 1 END)     AS triage,
                COUNT(DISTINCT c.id_pac)                                     AS pacientes_unicos,
                COUNT(DISTINCT c.id_esp)                                     AS especialidades_activas,
                COUNT(DISTINCT CASE
                    WHEN (
                        SELECT COUNT(*) FROM cita c2
                        WHERE c2.id_pac = c.id_pac
                          AND c2.estado_cit = 'Finalizado'
                          AND c2.fec_cit BETWEEN :inicio1 AND :fin1
                    ) > 1 THEN c.id_pac
                END) AS pacientes_recurrentes,
                COUNT(DISTINCT CASE
                    WHEN (
                        SELECT COUNT(*) FROM cita c2
                        WHERE c2.id_pac = c.id_pac
                          AND c2.estado_cit = 'Finalizado'
                          AND c2.fec_cit BETWEEN :inicio2 AND :fin2
                    ) = 1 THEN c.id_pac
                END) AS pacientes_fuga
            FROM cita c
            WHERE c.fec_cit BETWEEN :inicio3 AND :fin3
        ", [
            'inicio1' => $inicio, 'fin1' => $fin,
            'inicio2' => $inicio, 'fin2' => $fin,
            'inicio3' => $inicio, 'fin3' => $fin,
        ]);

        $totalCitas = max((int) $row->total_citas, 1);
        $pacUnicos  = max((int) $row->pacientes_unicos, 1);

        return [
            'total_citas'            => (int) $row->total_citas,
            'finalizadas'            => (int) $row->finalizadas,
            'canceladas'             => (int) $row->canceladas,
            'reservadas'             => (int) $row->reservadas,
            'triage'                 => (int) $row->triage,
            'pacientes_unicos'       => (int) $row->pacientes_unicos,
            'especialidades_activas' => (int) $row->especialidades_activas,
            'pacientes_fuga_count'   => (int) $row->pacientes_fuga,
            'pacientes_recurrentes'  => (int) $row->pacientes_recurrentes,
            'pct_finalizadas'        => round($row->finalizadas / $totalCitas * 100, 1),
            'pct_canceladas'         => round($row->canceladas  / $totalCitas * 100, 1),
            'tasa_recurrencia'       => round($row->pacientes_recurrentes / $pacUnicos * 100, 1),
            'tasa_fuga_potencial'    => round($row->pacientes_fuga        / $pacUnicos * 100, 1),
            'tasa_cancelacion'       => round($row->canceladas            / $totalCitas * 100, 1),
            'periodo'                => ['inicio' => $inicio, 'fin' => $fin],
        ];
    }

    private function calcEstados(string $inicio, string $fin): array
    {
        $rows = DB::select("
            SELECT
                estado_cit                                          AS estado,
                COUNT(*)                                            AS cantidad,
                ROUND(COUNT(*)::NUMERIC / SUM(COUNT(*)) OVER () * 100, 1) AS porcentaje
            FROM cita
            WHERE fec_cit BETWEEN :inicio AND :fin
            GROUP BY estado_cit
            ORDER BY cantidad DESC
        ", ['inicio' => $inicio, 'fin' => $fin]);

        $colores = [
            'Finalizado' => '#2E7D32',
            'Cancelado'  => '#D94040',
            'Reservado'  => '#1565C0',
            'Triage'     => '#F5A623',
        ];

        $total = collect($rows)->sum('cantidad');
        $data  = collect($rows)->map(fn ($r) => [
            'estado'     => $r->estado,
            'cantidad'   => (int) $r->cantidad,
            'porcentaje' => (float) $r->porcentaje,
            'color'      => $colores[$r->estado] ?? '#9CA3AF',
        ])->values()->all();

        return [
            'total'   => (int) $total,
            'estados' => $data,
            'chart'   => [
                'labels'     => collect($data)->pluck('estado')->all(),
                'data'       => collect($data)->pluck('cantidad')->all(),
                'colors'     => collect($data)->pluck('color')->all(),
                'porcentajes'=> collect($data)->pluck('porcentaje')->all(),
            ],
        ];
    }

    private function calcEspecialidades(string $inicio, string $fin): array
    {
        $rows = DB::select("
            SELECT
                e.nom_esp                                                   AS especialidad,
                COUNT(c.id_cit)                                             AS total_citas,
                COUNT(DISTINCT c.id_pac)                                    AS pacientes_unicos,
                COUNT(CASE WHEN c.estado_cit = 'Cancelado' THEN 1 END)     AS canceladas,
                ROUND(
                    COUNT(CASE WHEN c.estado_cit = 'Cancelado' THEN 1 END)::NUMERIC
                    / NULLIF(COUNT(c.id_cit), 0) * 100
                , 1) AS pct_cancelacion
            FROM cita c
            JOIN especialidad e ON e.id_esp = c.id_esp
            WHERE c.fec_cit BETWEEN :inicio AND :fin
            GROUP BY e.id_esp, e.nom_esp
            ORDER BY total_citas DESC
            LIMIT 10
        ", ['inicio' => $inicio, 'fin' => $fin]);

        $maxCitas = collect($rows)->max('total_citas') ?: 1;

        $data = collect($rows)->map(fn ($r, $i) => [
            'posicion'          => $i + 1,
            'especialidad'      => $r->especialidad,
            'total_citas'       => (int) $r->total_citas,
            'pacientes_unicos'  => (int) $r->pacientes_unicos,
            'canceladas'        => (int) $r->canceladas,
            'pct_cancelacion'   => (float) ($r->pct_cancelacion ?? 0),
            'es_lider'          => $i === 0,
            'pct_barra'         => round($r->total_citas / $maxCitas * 100, 1),
            'color_cancelacion' => match (true) {
                (float) ($r->pct_cancelacion ?? 0) > 20 => 'rojo',
                (float) ($r->pct_cancelacion ?? 0) > 10 => 'naranja',
                default                                  => 'verde',
            },
        ])->values()->all();

        return [
            'especialidades' => $data,
            'lider'          => $data[0] ?? null,
            'chart'          => [
                'labels' => collect($data)->pluck('especialidad')->all(),
                'data'   => collect($data)->pluck('total_citas')->all(),
                'colors' => collect($data)->map(fn ($e) =>
                    $e['es_lider'] ? '#F5A623' : '#1A3A6B'
                )->all(),
            ],
        ];
    }

    private function calcSeguros(string $inicio, string $fin): array
    {
        $rows = DB::select("
            SELECT
                p.seguro_pac                                                  AS seguro,
                COUNT(c.id_cit)                                               AS total,
                COUNT(CASE WHEN c.estado_cit = 'Finalizado'  THEN 1 END)     AS finalizadas,
                COUNT(CASE WHEN c.estado_cit = 'Cancelado'   THEN 1 END)     AS canceladas,
                ROUND(
                    COUNT(CASE WHEN c.estado_cit = 'Cancelado' THEN 1 END)::NUMERIC
                    / NULLIF(COUNT(c.id_cit), 0) * 100
                , 1) AS pct_cancelacion
            FROM cita c
            JOIN paciente p ON p.id_pac = c.id_pac
            WHERE c.fec_cit BETWEEN :inicio AND :fin
            GROUP BY p.seguro_pac
            ORDER BY total DESC
        ", ['inicio' => $inicio, 'fin' => $fin]);

        $nombres = [
            'Particular'             => 'PARTICULAR',
            'Alianza_Seguros'        => 'ALIANZA SEGUROS S.A.',
            'Bisa_Seguros'           => 'BISA SEGUROS Y REASEGUROS S.A.',
            'Univida'                => 'UNIVIDA',
            'Nacional_Seguros'       => 'NACIONAL SEGUROS VIDA Y SALUD S.A.',
            'Seguros_Internacionales'=> 'SEGURO EXTRANJERO',
        ];

        $data = collect($rows)->map(fn ($r, $i) => [
            'seguro'            => $nombres[$r->seguro] ?? strtoupper($r->seguro),
            'seguro_raw'        => $r->seguro,
            'total'             => (int) $r->total,
            'finalizadas'       => (int) $r->finalizadas,
            'canceladas'        => (int) $r->canceladas,
            'pct_cancelacion'   => (float) ($r->pct_cancelacion ?? 0),
            'es_lider'          => $i === 0,
            'color_cancelacion' => match (true) {
                (float) ($r->pct_cancelacion ?? 0) > 20 => 'rojo',
                (float) ($r->pct_cancelacion ?? 0) > 10 => 'naranja',
                default                                  => 'verde',
            },
        ])->values()->all();

        $top4 = collect($data)->take(4);

        return [
            'seguros' => $data,
            'chart'   => [
                'labels'      => $top4->pluck('seguro')->all(),
                'finalizadas' => $top4->pluck('finalizadas')->all(),
                'canceladas'  => $top4->pluck('canceladas')->all(),
                'totales'     => $top4->pluck('total')->all(),
                'colores'     => ['finalizadas' => '#2E7D32', 'canceladas' => '#D94040'],
            ],
        ];
    }

    private function calcAlertas(): array
    {
        $rows = DB::select("
            SELECT
                id_seg,
                nombre_paciente,
                ci_pac,
                tel_pac,
                nombre_medico,
                especialidad,
                fecha_consulta,
                fecha_esperada_retorno,
                estado_seg,
                fecha_alerta_medico,
                alerta_admin,
                fec_alerta_admin,
                CASE WHEN fec_alerta_admin IS NOT NULL
                     THEN EXTRACT(DAY FROM NOW() - fec_alerta_admin)
                     ELSE 0
                END AS dias_sin_respuesta
            FROM vw_alertas_fuga
            ORDER BY dias_sin_respuesta DESC NULLS LAST
            LIMIT 20
        ");

        $alertas = collect($rows)->map(fn ($a) => [
            'id_seg'                  => $a->id_seg,
            'paciente'                => $a->nombre_paciente,
            'ci'                      => $a->ci_pac,
            'telefono'                => $a->tel_pac,
            'medico'                  => $a->nombre_medico,
            'especialidad'            => $a->especialidad,
            'fecha_consulta'          => $a->fecha_consulta
                ? Carbon::parse($a->fecha_consulta)->format('d/m/Y')
                : '—',
            'fecha_retorno_esperada'  => $a->fecha_esperada_retorno
                ? Carbon::parse($a->fecha_esperada_retorno)->format('d/m/Y')
                : '—',
            'estado'                  => $a->estado_seg,
            'dias_sin_respuesta'      => (int) $a->dias_sin_respuesta,
            'urgencia'                => match (true) {
                (int) $a->dias_sin_respuesta > 7 => 'critica',
                (int) $a->dias_sin_respuesta > 3 => 'alta',
                default                           => 'media',
            },
        ])->values()->all();

        return ['total' => count($alertas), 'alertas' => $alertas];
    }
}
