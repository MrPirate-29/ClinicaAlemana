<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KpisSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        private string $inicio,
        private string $fin
    ) {}

    public function title(): string { return 'KPIs EJECUTIVOS'; }

    public function headings(): array
    {
        return ['INDICADOR', 'VALOR', 'OBSERVACIÓN'];
    }

    public function array(): array
    {
        $row = DB::selectOne("
            SELECT
                COUNT(id_cit)                                                 AS total_citas,
                COUNT(CASE WHEN estado_cit = 'Finalizado'  THEN 1 END)       AS finalizadas,
                COUNT(CASE WHEN estado_cit = 'Cancelado'   THEN 1 END)       AS canceladas,
                COUNT(DISTINCT id_pac)                                        AS pacientes_unicos,
                COUNT(DISTINCT id_esp)                                        AS especialidades_activas
            FROM cita
            WHERE fec_cit BETWEEN :inicio AND :fin
        ", ['inicio' => $this->inicio, 'fin' => $this->fin]);

        $t = max((int) $row->total_citas, 1);
        $p = max((int) $row->pacientes_unicos, 1);

        return [
            ['PERÍODO',                  "{$this->inicio} — {$this->fin}", 'Período analizado'],
            ['Total Citas Registradas',  $row->total_citas,   ''],
            ['Citas Finalizadas',        $row->finalizadas,   round($row->finalizadas / $t * 100, 1) . '% del total'],
            ['Citas Canceladas',         $row->canceladas,    round($row->canceladas  / $t * 100, 1) . '% del total'],
            ['Pacientes Únicos',         $row->pacientes_unicos, ''],
            ['Especialidades Activas',   $row->especialidades_activas, ''],
            ['Tasa de Cancelación',      round($row->canceladas / $t * 100, 1) . '%', ''],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '1A3A6B']]],
        ];
    }
}
