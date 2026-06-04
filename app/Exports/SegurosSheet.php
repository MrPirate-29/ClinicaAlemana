<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SegurosSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private string $inicio, private string $fin) {}

    public function title(): string { return 'DISTRIBUCIÓN POR SEGURO'; }

    public function headings(): array
    {
        return ['SEGURO', 'TOTAL', 'FINALIZADAS', 'CANCELADAS', 'CANCEL%'];
    }

    public function array(): array
    {
        $rows = DB::select("
            SELECT p.seguro_pac, COUNT(c.id_cit) AS total,
                   COUNT(CASE WHEN c.estado_cit='Finalizado' THEN 1 END) AS finalizadas,
                   COUNT(CASE WHEN c.estado_cit='Cancelado'  THEN 1 END) AS canceladas,
                   ROUND(COUNT(CASE WHEN c.estado_cit='Cancelado' THEN 1 END)::NUMERIC
                         / NULLIF(COUNT(c.id_cit),0)*100,1) AS pct
            FROM cita c JOIN paciente p ON p.id_pac = c.id_pac
            WHERE c.fec_cit BETWEEN :inicio AND :fin
            GROUP BY p.seguro_pac ORDER BY total DESC
        ", ['inicio' => $this->inicio, 'fin' => $this->fin]);

        return collect($rows)->map(fn ($r) =>
            [$r->seguro_pac, $r->total, $r->finalizadas, $r->canceladas, ($r->pct ?? 0) . '%']
        )->all();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '1A3A6B']]],
        ];
    }
}
