<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use App\Models\SeguimientoPaciente;
use App\Models\Usuario;
use Illuminate\Console\Command;

class VerificarSeguimientosPendientes extends Command
{
    protected $signature   = 'seguimiento:verificar';
    protected $description = 'Verifica seguimientos vencidos y escala fugas sospechosas al Administrador';

    public function handle(): void
    {
        // 1. Seguimientos vencidos sin alerta enviada al médico
        $vencidos = SeguimientoPaciente::where('estado_seg', 'Pendiente')
            ->where('fec_proxima_esp', '<', now()->toDateString())
            ->where('alerta_enviada', false)
            ->with(['paciente', 'medico.usuario'])
            ->get();

        foreach ($vencidos as $seg) {
            $nomPac = trim(($seg->paciente->nom_pac ?? '') . ' ' . ($seg->paciente->apat_pac ?? ''));

            Notificacion::create([
                'tit_not'    => 'Reconsulta vencida sin reporte',
                'men_not'    => "El paciente {$nomPac} debía retornar el {$seg->fec_proxima_esp->format('d/m/Y')}. Por favor registra si asistió o no.",
                'tip_not'    => 'Alerta_Fuga',
                'canal_not'  => 'Interna',
                'reftab_not' => 'seguimiento_paciente',
                'refid_not'  => $seg->id_seg,
                'leido_not'  => false,
                'fec_not'    => now(),
                'id_usu'     => $seg->medico->usuario->id_usu,
            ]);

            $seg->update([
                'alerta_enviada' => true,
                'fec_alerta'     => now()->toDateString(),
            ]);
        }

        // 2. Seguimientos alertados al médico hace más de 2 días sin respuesta → escalar
        $sinRespuesta = SeguimientoPaciente::where('estado_seg', 'Pendiente')
            ->where('alerta_enviada', true)
            ->where('alerta_admin', false)
            ->where('fec_alerta', '<', now()->subDays(2)->toDateString())
            ->with(['paciente', 'medico.usuario', 'reporte.cita.especialidad'])
            ->get();

        $idAdmin = Usuario::whereHas('rol', fn ($q) =>
            $q->where('nom_rol', 'Administrador')
        )->value('id_usu');

        foreach ($sinRespuesta as $seg) {
            $seg->update([
                'estado_seg'       => 'Fuga_Sospechosa',
                'alerta_admin'     => true,
                'fec_alerta_admin' => now(),
            ]);

            if ($idAdmin) {
                $nomMed  = trim(($seg->medico->usuario->nom_usu ?? '') . ' ' . ($seg->medico->usuario->apat_usu ?? ''));
                $nomPac  = trim(($seg->paciente->nom_pac ?? '') . ' ' . ($seg->paciente->apat_pac ?? ''));
                $espNom  = $seg->reporte?->cita?->especialidad?->nom_esp ?? 'Especialidad desconocida';

                Notificacion::create([
                    'tit_not'    => 'Fuga sospechosa — Sin reporte del médico',
                    'men_not'    => "El Dr. {$nomMed} no reportó si {$nomPac} ({$espNom}) retornó. Lleva más de 2 días sin respuesta. Posible captación privada.",
                    'tip_not'    => 'Alerta_Fuga',
                    'canal_not'  => 'Interna',
                    'reftab_not' => 'seguimiento_paciente',
                    'refid_not'  => $seg->id_seg,
                    'leido_not'  => false,
                    'fec_not'    => now(),
                    'id_usu'     => $idAdmin,
                ]);
            }
        }

        $this->info("Verificación completada: {$vencidos->count()} alertados al médico, {$sinRespuesta->count()} escalados al admin.");
    }
}
