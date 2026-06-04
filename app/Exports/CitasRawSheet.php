<?php

namespace App\Exports;

use App\Models\Cita;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CitasRawSheet implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        private string $inicio,
        private string $fin
    ) {}

    public function title(): string
    {
        return 'BASE DE DATOS LIMPIA';
    }

    public function headings(): array
    {
        return [
            'FECHA', 'HORA', 'ESTADO', 'NOMBRE DEL PACIENTE',
            'ID DOCUMENTO', 'TELEFONO', 'SEGURO', 'ESPECIALIDAD',
            'MÉDICO', 'TIPO DE CONSULTA', 'SUCURSAL',
        ];
    }

    public function query(): Builder
    {
        return Cita::with(['paciente', 'especialidad', 'medico.usuario'])
            ->whereBetween('fec_cit', [$this->inicio, $this->fin])
            ->orderBy('fec_cit')
            ->orderBy('hora_cit');
    }

    public function map($cita): array
    {
        $med = $cita->medico?->usuario;
        return [
            $cita->fec_cit?->format('d/m/Y'),
            substr($cita->hora_cit ?? '', 0, 5),
            $cita->estado_cit,
            trim(($cita->paciente?->nom_pac ?? '') . ' ' . ($cita->paciente?->apat_pac ?? '')),
            $cita->paciente?->ci_pac,
            $cita->paciente?->tel_pac,
            $cita->paciente?->seguro_pac,
            $cita->especialidad?->nom_esp,
            trim(($med?->apat_usu ?? '') . ' ' . ($med?->nom_usu ?? '')),
            $cita->tipo_cit,
            'Centro Médico Alemana — Achumani',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'color' => ['rgb' => '1A3A6B']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
