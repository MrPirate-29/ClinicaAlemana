<?php

namespace App\Http\Controllers\Gestora;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConsultaController extends Controller
{
    // ─── GET /gestora/consultas/historial ───────────────────────────────────
    public function historial(Request $request)
    {
        $usuario = Auth::user();

        $query = Cita::with(['paciente', 'medico.usuario', 'especialidad'])
            ->orderByDesc('fec_cit')
            ->orderByDesc('hora_cit');

        // Filtro búsqueda (nombre o CI del paciente)
        if ($busqueda = $request->input('q')) {
            $query->whereHas('paciente', function ($q) use ($busqueda) {
                $q->where('nom_pac',  'ilike', "%{$busqueda}%")
                  ->orWhere('apat_pac', 'ilike', "%{$busqueda}%")
                  ->orWhere('ci_pac',   'ilike', "%{$busqueda}%");
            });
        }

        // Filtro estado
        if ($estado = $request->input('estado')) {
            $query->where('estado_cit', $estado);
        }

        // Filtro especialidad
        if ($esp = $request->input('esp')) {
            $query->where('id_esp', $esp);
        }

        // Filtro fechas
        if ($desde = $request->input('desde')) {
            $query->whereDate('fec_cit', '>=', $desde);
        }
        if ($hasta = $request->input('hasta')) {
            $query->whereDate('fec_cit', '<=', $hasta);
        }

        $citas          = $query->paginate(6)->withQueryString();
        $especialidades = Especialidad::where('activo_esp', true)->orderBy('nom_esp')->get(['id_esp','nom_esp']);

        // Contadores rápidos para los KPI cards
        $totalHoy        = Cita::whereDate('fec_cit', today())->count();
        $totalMes        = Cita::whereMonth('fec_cit', now()->month)->whereYear('fec_cit', now()->year)->count();
        $canceladosMes   = Cita::whereMonth('fec_cit', now()->month)->whereYear('fec_cit', now()->year)->where('estado_cit','Cancelado')->count();
        $reservadosMes   = Cita::whereMonth('fec_cit', now()->month)->whereYear('fec_cit', now()->year)->where('estado_cit','Reservado')->count();

        return view('gestora.consultas.historial', compact(
            'usuario','citas','especialidades',
            'totalHoy','totalMes','canceladosMes','reservadosMes'
        ));
    }

    // ─── GET /gestora/panel  o  GET /gestora/consultas/nueva ───────────────
    public function index()
    {
        $usuario      = Auth::user();
        $especialidades = Especialidad::where('activo_esp', true)
                            ->orderBy('nom_esp')
                            ->get(['id_esp', 'nom_esp']);

        return view('gestora.consultas.nueva', compact('usuario', 'especialidades'));
    }

    // ─── POST /gestora/consultas/paso1 ──────────────────────────────────────
    public function storePaso1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_pac'    => 'required|string|max:100',
            'apat_pac'   => 'nullable|string|max:100',
            'ci_pac'     => 'required|string|max:30',
            'tel_pac'    => 'nullable|string|max:25',
            'seguro_pac' => 'required|in:Particular,Alianza_Seguros,Bisa_Seguros,Univida,Nacional_Seguros,Seguros_Internacionales',
            'priv_pac'   => 'nullable|boolean',
        ], [
            'nom_pac.required'    => 'El nombre del paciente es obligatorio.',
            'ci_pac.required'     => 'El CI/documento es obligatorio.',
            'seguro_pac.required' => 'Debe seleccionar el seguro médico.',
            'seguro_pac.in'       => 'El seguro seleccionado no es válido.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Guardar en sesión antes de verificar duplicado
        session([
            'paso1'         => $request->only(['nom_pac','apat_pac','ci_pac','tel_pac','seguro_pac','priv_pac']),
            'pac_existente' => false,
        ]);

        // Verificar CI duplicado (no bloqueante)
        $existente = Paciente::where('ci_pac', $request->ci_pac)->first();

        if ($existente) {
            return response()->json([
                'success'            => true,
                'ci_duplicado'       => true,
                'paciente_existente' => [
                    'id_pac'     => $existente->id_pac,
                    'nombre'     => trim($existente->nom_pac . ' ' . $existente->apat_pac),
                    'ci_pac'     => $existente->ci_pac,
                    'tel_pac'    => $existente->tel_pac,
                    'seguro_pac' => $existente->seguro_pac,
                ],
                'datos_nuevos' => $request->only(['nom_pac','apat_pac','ci_pac','tel_pac','seguro_pac','priv_pac']),
            ]);
        }

        return response()->json(['success' => true, 'ci_duplicado' => false, 'paso_actual' => 2]);
    }

    // ─── POST /gestora/consultas/paso1/existente ────────────────────────────
    public function usarExistente(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pac' => 'required|integer|exists:paciente,id_pac',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        session(['pac_existente' => true, 'id_pac_existente' => $request->id_pac]);

        return response()->json(['success' => true, 'paso_actual' => 2]);
    }

    // ─── POST /gestora/consultas/paso2 ──────────────────────────────────────
    public function storePaso2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_esp'   => 'required|integer|exists:especialidad,id_esp',
            'tipo_cit' => 'required|in:Consulta,Reconsulta_0_7,Reconsulta_8_15',
            'motivo'   => 'nullable|string|max:500',
            'fecha'    => 'nullable|date',
        ], [
            'id_esp.required'   => 'Debe seleccionar una especialidad.',
            'tipo_cit.required' => 'Debe seleccionar el tipo de consulta.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        session(['paso2' => $request->only(['id_esp','tipo_cit','motivo'])]);

        $fecha     = $request->input('fecha', Carbon::today()->toDateString());
        $medicos   = $this->getMedicosData((int) $request->id_esp, $fecha);

        return response()->json([
            'success'        => true,
            'paso_actual'    => 3,
            'disponibilidad' => $medicos,
        ]);
    }

    // ─── GET /gestora/consultas/medicos-disponibles ──────────────────────────
    public function getMedicosDisponibles(Request $request)
    {
        $idEsp = (int) $request->input('id_esp', session('paso2.id_esp'));
        $fecha = $request->input('fecha', Carbon::today()->toDateString());

        if (! $idEsp) {
            return response()->json(['error' => 'Especialidad requerida'], 400);
        }

        return response()->json($this->getMedicosData($idEsp, $fecha));
    }

    // ─── POST /gestora/consultas/confirmar ──────────────────────────────────
    public function confirmar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_med'    => 'required|integer|exists:medico,id_med',
            'hora_cit'  => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'fecha_cit' => 'required|date',
        ], [
            'id_med.required'   => 'Debe seleccionar un médico.',
            'hora_cit.required' => 'Debe seleccionar un horario.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if (! session('paso1') && ! session('pac_existente')) {
            return response()->json(['success' => false, 'message' => 'Sesión expirada. Por favor reinicia el proceso.'], 400);
        }

        try {
            $cita = DB::transaction(function () use ($request) {

                // 1. Crear o reutilizar paciente
                if (session('pac_existente')) {
                    $idPac = session('id_pac_existente');
                } else {
                    $datos               = session('paso1');
                    $datos['priv_pac']   = (bool) ($datos['priv_pac'] ?? false);
                    $datos['id_usu_gestora'] = Auth::id();

                    $paciente = Paciente::create($datos);
                    $idPac    = $paciente->id_pac;
                }

                // 2. Verificar que el slot sigue libre (race condition check)
                $conflicto = Cita::where('id_med',   $request->id_med)
                    ->whereDate('fec_cit',  $request->fecha_cit)
                    ->where('hora_cit', $request->hora_cit . ':00')
                    ->whereNotIn('estado_cit', ['Cancelado'])
                    ->exists();

                if ($conflicto) {
                    throw new \Exception('SLOT_OCUPADO');
                }

                // 3. Registrar la cita
                $paso2 = session('paso2');
                $cita  = Cita::create([
                    'fec_cit'        => $request->fecha_cit,
                    'hora_cit'       => $request->hora_cit . ':00',
                    'tipo_cit'       => $paso2['tipo_cit'],
                    'estado_cit'     => 'Reservado',
                    'motivo_cit'     => $paso2['motivo'] ?? null,
                    'id_pac'         => $idPac,
                    'id_med'         => $request->id_med,
                    'id_esp'         => $paso2['id_esp'],
                    'id_usu_gestora' => Auth::id(),
                ]);

                // 4. Cargar relaciones
                $cita->load(['paciente', 'medico.usuario', 'especialidad']);

                // 5. Limpiar sesión del wizard
                session()->forget(['paso1','paso2','pac_existente','id_pac_existente']);

                return $cita;
            });

            $pac = $cita->paciente;
            $med = $cita->medico->usuario;

            return response()->json([
                'success' => true,
                'cita'    => [
                    'id_cit'      => $cita->id_cit,
                    'paciente'    => trim($pac->nom_pac . ' ' . ($pac->apat_pac ?? '')),
                    'medico'      => trim(($med->apat_usu ?? '') . ' ' . $med->nom_usu),
                    'especialidad'=> $cita->especialidad->nom_esp,
                    'fecha'       => Carbon::parse($cita->fec_cit)->translatedFormat('d \\de F \\de Y'),
                    'hora'        => substr($cita->hora_cit, 0, 5),
                    'tipo'        => $this->labelTipo($cita->tipo_cit),
                ],
            ]);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'SLOT_OCUPADO') {
                return response()->json([
                    'success'      => false,
                    'slot_ocupado' => true,
                    'message'      => 'Este horario acaba de ser tomado. Por favor selecciona otro.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la cita. Intenta nuevamente.',
            ], 500);
        }
    }

    // ─── GET /gestora/pacientes ───────────────────────────────────────────────
    public function pacientes(Request $request)
    {
        $query = Paciente::withCount('citas')->orderByDesc('fecreg_pac');

        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nom_pac',  'ilike', "%{$q}%")
                    ->orWhere('apat_pac', 'ilike', "%{$q}%")
                    ->orWhere('ci_pac',   'ilike', "%{$q}%");
            });
        }

        if ($seguro = $request->input('seguro')) {
            $query->where('seguro_pac', $seguro);
        }

        $pacientes     = $query->paginate(6)->withQueryString();
        $total         = Paciente::count();
        $totalPrivados = Paciente::where('priv_pac', true)->count();
        $totalMes      = Paciente::whereMonth('fecreg_pac', now()->month)
                                 ->whereYear('fecreg_pac', now()->year)->count();

        return view('gestora.pacientes', compact('pacientes', 'total', 'totalPrivados', 'totalMes'));
    }

    // ─── GET /gestora/perfil ──────────────────────────────────────────────────
    public function perfil()
    {
        return view('gestora.perfil', ['usuario' => Auth::user()]);
    }

    // ─── PUT /gestora/perfil ──────────────────────────────────────────────────
    public function updatePerfil(Request $request)
    {
        $usuario = Auth::user();

        $validator = Validator::make($request->all(), [
            'nom_usu'  => 'required|string|max:100',
            'apat_usu' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $usuario->update($request->only('nom_usu', 'apat_usu'));

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    // ─── GET /gestora/notificaciones ──────────────────────────────────────────
    public function notificaciones()
    {
        return view('gestora.notificaciones', ['usuario' => Auth::user()]);
    }

    // ─── Helpers privados ────────────────────────────────────────────────────

    private function getMedicosData(int $idEsp, string $fecha): array
    {
        $fechaCarbon = Carbon::parse($fecha);
        $diasMap     = [0=>'Domingo',1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes',6=>'Sabado'];
        $diaSemana   = $diasMap[$fechaCarbon->dayOfWeek];

        $especialidad = Especialidad::find($idEsp);

        $rows = DB::table('medico as m')
            ->join('usuario as u',           'u.id_usu',  '=', 'm.id_usu')
            ->join('medico_especialidad as me','me.id_med', '=', 'm.id_med')
            ->join('horario_medico as h',     'h.id_med',  '=', 'm.id_med')
            ->where('me.id_esp',  $idEsp)
            ->where('m.est_med',  'ACTIVO')
            ->where('h.dia_hor',  $diaSemana)
            ->where('h.activo_hor', true)
            ->select('m.id_med','u.nom_usu','u.apat_usu','h.hora_ini','h.hora_fin')
            ->distinct()
            ->orderBy('u.apat_usu')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            // Slots ocupados ese día para este médico
            $ocupados = DB::table('cita')
                ->where('id_med', $row->id_med)
                ->whereDate('fec_cit', $fecha)
                ->whereNotIn('estado_cit', ['Cancelado'])
                ->pluck('hora_cit')
                ->map(fn ($h) => substr($h, 0, 5))
                ->toArray();

            // Generar slots de 30 min
            $slots   = [];
            $inicio  = Carbon::parse($row->hora_ini);
            $fin     = Carbon::parse($row->hora_fin);

            while ($inicio < $fin) {
                $hora    = $inicio->format('H:i');
                $slots[] = ['hora' => $hora, 'disponible' => ! in_array($hora, $ocupados)];
                $inicio->addMinutes(30);
            }

            $tieneDisp = collect($slots)->contains('disponible', true);

            $result[] = [
                'id_med'              => $row->id_med,
                'nombre_completo'     => trim(($row->apat_usu ?? '') . ' ' . $row->nom_usu),
                'inicial'             => strtoupper(substr($row->apat_usu ?? 'M', 0, 1)),
                'tiene_disponibilidad'=> $tieneDisp,
                'slots'               => $slots,
            ];
        }

        usort($result, fn ($a, $b) => $b['tiene_disponibilidad'] <=> $a['tiene_disponibilidad']);

        return [
            'especialidad'  => $especialidad?->nom_esp ?? '',
            'fecha'         => $fecha,
            'dia'           => $diaSemana,
            'total_medicos' => count($result),
            'medicos'       => $result,
        ];
    }

    private function labelTipo(string $tipo): string
    {
        return match ($tipo) {
            'Consulta'        => 'Consulta Regular',
            'Reconsulta_0_7'  => 'Reconsulta (0-7 días)',
            'Reconsulta_8_15' => 'Reconsulta (8-15 días)',
            'Campana'         => 'Consulta de Campaña',
            default           => $tipo,
        };
    }
}
