<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EspecialidadesSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private string $inicio, private string $fin) {}

    public function title(): string { return 'TOP ESPECIALIDADES'; }

    public function headings(): array
    {
        return ['#', 'ESPECIALIDAD', 'TOTAL CITAS', 'PAC. ÚNICOS', 'CANCELADAS', 'CANCEL%'];
    }

    public function array(): array
    {
        $rows = DB::select("
            SELECT e.nom_esp, COUNT(c.id_cit) AS total,
                   COUNT(DISTINCT c.id_pac) AS unicos,
                   COUNT(CASE WHEN c.estado_cit='Cancelado' THEN 1 END) AS canceladas,
                   ROUND(COUNT(CASE WHEN c.estado_cit='Cancelado' THEN 1 END)::NUMERIC
                         / NULLIF(COUNT(c.id_cit),0)*100,1) AS pct
            FROM cita c JOIN especialidad e ON e.id_esp = c.id_esp
            WHERE c.fec_cit BETWEEN :inicio AND :fin
            GROUP BY e.id_esp, e.nom_esp ORDER BY total DESC LIMIT 10
        ", ['inicio' => $this->inicio, 'fin' => $this->fin]);

        return collect($rows)->map(fn ($r, $i) =>
            [$i + 1, $r->nom_esp, $r->total, $r->unicos, $r->canceladas, ($r->pct ?? 0) . '%']
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
