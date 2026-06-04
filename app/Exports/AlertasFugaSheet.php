<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AlertasFugaSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private string $inicio, private string $fin) {}

    public function title(): string { return 'ALERTAS DE FUGA'; }

    public function headings(): array
    {
        return [
            'PACIENTE', 'CI', 'TELÉFONO', 'MÉDICO',
            'ESPECIALIDAD', 'FECHA CONSULTA', 'RETORNO ESPERADO', 'ESTADO', 'DÍAS SIN RESP.',
        ];
    }

    public function array(): array
    {
        $rows = DB::select("
            SELECT nombre_paciente, ci_pac, tel_pac, nombre_medico, especialidad,
                   fecha_consulta, fecha_esperada_retorno, estado_seg,
                   CASE WHEN fec_alerta_admin IS NOT NULL
                        THEN EXTRACT(DAY FROM NOW() - fec_alerta_admin) ELSE 0 END AS dias
            FROM vw_alertas_fuga
            ORDER BY dias DESC NULLS LAST LIMIT 50
        ");

        return collect($rows)->map(fn ($r) => [
            $r->nombre_paciente, $r->ci_pac, $r->tel_pac, $r->nombre_medico,
            $r->especialidad, $r->fecha_consulta, $r->fecha_esperada_retorno,
            $r->estado_seg, (int) $r->dias,
        ])->all();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'D94040']]],
        ];
    }
}
