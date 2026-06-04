<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EstadosSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private string $inicio, private string $fin) {}

    public function title(): string { return 'ESTADO DE CITAS'; }

    public function headings(): array
    {
        return ['ESTADO', 'CANTIDAD', '% DEL TOTAL'];
    }

    public function array(): array
    {
        $rows = DB::select("
            SELECT estado_cit AS estado, COUNT(*) AS cantidad,
                   ROUND(COUNT(*)::NUMERIC / SUM(COUNT(*)) OVER () * 100, 1) AS porcentaje
            FROM cita WHERE fec_cit BETWEEN :inicio AND :fin
            GROUP BY estado_cit ORDER BY cantidad DESC
        ", ['inicio' => $this->inicio, 'fin' => $this->fin]);

        $total = collect($rows)->sum('cantidad');
        $data  = collect($rows)->map(fn ($r) => [$r->estado, $r->cantidad, $r->porcentaje . '%'])->all();
        $data[] = ['TOTAL', $total, '100.0%'];
        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '1A3A6B']]],
        ];
    }
}
