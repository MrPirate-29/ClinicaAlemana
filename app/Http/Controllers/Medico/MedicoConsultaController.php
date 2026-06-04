<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReporteRequest;
use App\Models\Cita;
use App\Models\Medico;
use App\Models\Notificacion;
use App\Models\ReporteConsulta;
use App\Models\SeguimientoPaciente;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicoConsultaController extends Controller
{
    // ─── GET /medico/consultas ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $usuario = Auth::user();
        $idMed   = Medico::where('id_usu', $usuario->id_usu)->value('id_med');

        $inicio = $request->input('desde')
            ? Carbon::parse($request->input('desde'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $fin = $request->input('hasta')
            ? Carbon::parse($request->input('hasta'))->endOfDay()
            : Carbon::now()->endOfMonth();

        // KPI 1: Total consultas del período
        $totalConsultas = Cita::where('id_med', $idMed)
            ->whereBetween('fec_cit', [$inicio, $fin])
            ->count();

        // KPI 2: Pendientes de reporte (sin reporte aún)
        $pendientes = Cita::where('id_med', $idMed)
            ->whereBetween('fec_cit', [$inicio, $fin])
            ->whereDoesntHave('reporteConsulta')
            ->count();

        // KPI 3: Alertas activas escaladas al administrador
        $alertasActivas = SeguimientoPaciente::where('id_med', $idMed)
            ->where('estado_seg', 'Fuga_Sospechosa')
            ->where('alerta_admin', true)
            ->count();

        // Tabla de consultas con filtros
        $query = Cita::with([
                'paciente',
                'especialidad',
                'reporteConsulta',
                'reporteConsulta.seguimiento',
            ])
            ->where('id_med', $idMed)
            ->whereBetween('fec_cit', [$inicio, $fin]);

        if ($q = $request->input('q')) {
            $query->whereHas('paciente', fn ($pq) =>
                $pq->where('nom_pac',  'ilike', "%{$q}%")
                   ->orWhere('apat_pac', 'ilike', "%{$q}%")
                   ->orWhere('ci_pac',   'ilike', "%{$q}%")
            );
        }

        $consultas = $query
            ->orderByDesc('fec_cit')
            ->orderByDesc('hora_cit')
            ->paginate(6)
            ->withQueryString();

        // Calcular estado visual y fecha de próxima reconsulta por fila
        $consultas->getCollection()->transform(function ($cita) {
            $reporte     = $cita->reporteConsulta;
            $seguimiento = $reporte?->seguimiento;

            if (!$reporte) {
                $diasDesde = Carbon::parse($cita->fec_cit)->diffInDays(now());
                $cita->estado_visual = $diasDesde > 2 ? 'Alerta' : 'Pendiente';
            } elseif ($seguimiento) {
                $cita->estado_visual = match ($seguimiento->estado_seg) {
                    'Retorno_Confirmado' => 'Retorno Confirmado',
                    'Fuga_Confirmada'    => 'Fuga Confirmada',
                    'Fuga_Sospechosa'    => 'Alerta — Sin reporte',
                    default              => 'Reportado',
                };
            } else {
                $cita->estado_visual = 'Reportado';
            }

            $cita->prox_reconsulta = match ($cita->tipo_cit) {
                'Reconsulta_0_7'  => Carbon::parse($cita->fec_cit)->addDays(7)->format('d/m/Y'),
                'Reconsulta_8_15' => Carbon::parse($cita->fec_cit)->addDays(15)->format('d/m/Y'),
                default           => null,
            };

            // Marcar si la fecha de reconsulta ya venció
            if ($cita->prox_reconsulta) {
                $proxCarbon = Carbon::createFromFormat('d/m/Y', $cita->prox_reconsulta);
                $cita->prox_vencida = $proxCarbon->isPast() && $cita->estado_visual !== 'Reportado'
                    && $cita->estado_visual !== 'Retorno Confirmado';
            } else {
                $cita->prox_vencida = false;
            }

            return $cita;
        });

        return view('medico.consultas', compact(
            'usuario', 'consultas',
            'totalConsultas', 'pendientes', 'alertasActivas',
            'inicio', 'fin', 'idMed'
        ));
    }

    // ─── GET /medico/consultas/{id}/reporte ──────────────────────────────────
    public function getReporte($id)
    {
        $idMed = Medico::where('id_usu', auth()->id())->value('id_med');
        $cita  = Cita::with(['paciente', 'especialidad', 'reporteConsulta'])->findOrFail($id);

        abort_if((int) $cita->id_med !== (int) $idMed, 403);

        return response()->json([
            'cita' => [
                'id_cit'       => $cita->id_cit,
                'fec_cit'      => Carbon::parse($cita->fec_cit)->format('Y-m-d'),
                'fec_display'  => Carbon::parse($cita->fec_cit)->format('d/m/Y'),
                'hora_cit'     => substr($cita->hora_cit ?? '', 0, 5),
                'tipo_cit'     => $cita->tipo_cit,
                'motivo_cit'   => $cita->motivo_cit,
                'ya_reportado' => !is_null($cita->reporteConsulta),
            ],
            'paciente' => [
                'nombre'   => trim(($cita->paciente->nom_pac ?? '') . ' ' . ($cita->paciente->apat_pac ?? '')),
                'ci'       => $cita->paciente->ci_pac ?? '',
                'telefono' => $cita->paciente->tel_pac ?? '',
                'seguro'   => $cita->paciente->seguro_pac ?? '',
            ],
            'especialidad'      => $cita->especialidad->nom_esp ?? '',
            'reporte_existente' => $cita->reporteConsulta,
        ]);
    }

    // ─── POST /medico/consultas/{id}/reporte ─────────────────────────────────
    public function storeReporte(StoreReporteRequest $request, $id)
    {
        $idMed = Medico::where('id_usu', auth()->id())->value('id_med');

        $result = DB::transaction(function () use ($request, $id, $idMed) {

            $cita = Cita::findOrFail($id);
            abort_if((int) $cita->id_med !== (int) $idMed, 403);

            $validated = $request->validated();

            // 1. Crear el reporte
            $reporte = ReporteConsulta::create([
                'diagnostico_rep'   => $validated['diagnostico_rep'] ?? null,
                'fec_atencion'      => $cita->fec_cit,
                'tipo_rep'          => $validated['tipo_rep'],
                'paciente_asistio'  => (bool) $validated['paciente_asistio'],
                'motivo_no_asistio' => $validated['motivo_no_asistio'] ?? null,
                'motivo_otro_rep'   => $validated['motivo_otro_rep'] ?? null,
                'id_cit'            => $cita->id_cit,
                'id_med'            => $idMed,
            ]);

            // 2. Marcar la cita como Finalizado
            $cita->update(['estado_cit' => 'Finalizado']);

            // 3. Fecha de próxima reconsulta según tipo
            $fechaProxima = match ($validated['tipo_rep']) {
                'Reconsulta_0_7'  => Carbon::parse($cita->fec_cit)->addDays(7),
                'Reconsulta_8_15' => Carbon::parse($cita->fec_cit)->addDays(15),
                default           => null,
            };

            // 4. Crear seguimiento si aplica
            if ($fechaProxima || !(bool) $validated['paciente_asistio']) {

                $noAsistio    = !(bool) $validated['paciente_asistio'];
                $esDerivacion = ($validated['motivo_no_asistio'] ?? '') === 'Derivado_consulta_privada';

                $estadoSeg = match (true) {
                    $noAsistio  => 'Fuga_Confirmada',
                    default     => 'Pendiente',
                };

                SeguimientoPaciente::create([
                    'fec_proxima_esp' => $fechaProxima ?? now()->addDays(30),
                    'estado_seg'      => $estadoSeg,
                    'alerta_enviada'  => false,
                    'alerta_admin'    => false,
                    'id_rep'          => $reporte->id_rep,
                    'id_pac'          => $cita->id_pac,
                    'id_med'          => $idMed,
                ]);
            }

            // 5. Generar notificaciones
            $this->generarNotificacion($reporte, $validated, $cita);

            return $reporte;
        });

        return response()->json(['success' => true, 'id_rep' => $result->id_rep]);
    }

    // ─── GET /medico/alertas ─────────────────────────────────────────────────
    public function alertas()
    {
        $idMed = Medico::where('id_usu', auth()->id())->value('id_med');

        $alertas = SeguimientoPaciente::where('id_med', $idMed)
            ->whereIn('estado_seg', ['Fuga_Sospechosa', 'Fuga_Confirmada'])
            ->with(['paciente', 'reporte.cita.especialidad'])
            ->orderByDesc('fec_alerta_admin')
            ->get();

        return response()->json($alertas);
    }

    // ─── Privado: generar notificaciones ────────────────────────────────────
    private function generarNotificacion($reporte, $datos, $cita): void
    {
        $paciente = trim(($cita->paciente->nom_pac ?? '') . ' ' . ($cita->paciente->apat_pac ?? ''));

        if (!(bool) $datos['paciente_asistio']) {

            $esDerivacion = ($datos['motivo_no_asistio'] ?? '') === 'Derivado_consulta_privada';
            $idAdmin      = Usuario::whereHas('rol', fn ($q) =>
                $q->where('nom_rol', 'Administrador')
            )->value('id_usu');

            if ($idAdmin) {
                Notificacion::create([
                    'tit_not'    => $esDerivacion ? 'Posible fuga detectada' : 'Paciente no asistió a consulta',
                    'men_not'    => $esDerivacion
                        ? "El Dr. registró que {$paciente} fue derivado a consulta privada. Posible caso de fuga."
                        : "El paciente {$paciente} no asistió. Motivo: {$datos['motivo_no_asistio']}.",
                    'tip_not'    => $esDerivacion ? 'Alerta_Fuga' : 'Reporte',
                    'canal_not'  => 'Interna',
                    'reftab_not' => 'reporte_consulta',
                    'refid_not'  => $reporte->id_rep,
                    'leido_not'  => false,
                    'fec_not'    => now(),
                    'id_usu'     => $idAdmin,
                ]);
            }

        } else {

            $diasSeguimiento = match ($datos['tipo_rep']) {
                'Reconsulta_0_7'  => '7 días',
                'Reconsulta_8_15' => '15 días',
                default           => null,
            };

            if ($diasSeguimiento) {
                Notificacion::create([
                    'tit_not'    => 'Seguimiento activado',
                    'men_not'    => "Se activó seguimiento para {$paciente}. Plazo: {$diasSeguimiento}.",
                    'tip_not'    => 'Reconsulta',
                    'canal_not'  => 'Interna',
                    'reftab_not' => 'seguimiento_paciente',
                    'refid_not'  => null,
                    'leido_not'  => false,
                    'fec_not'    => now(),
                    'id_usu'     => auth()->id(),
                ]);
            }
        }
    }
}
