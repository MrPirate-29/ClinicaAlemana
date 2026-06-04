<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReporteCitasExport implements WithMultipleSheets
{
    public function __construct(
        private string $inicio,
        private string $fin,
        private array  $secciones
    ) {}

    public function sheets(): array
    {
        $hojas = [
            // Hoja 1 siempre incluida: datos crudos
            new CitasRawSheet($this->inicio, $this->fin),
        ];

        if (in_array('kpis', $this->secciones))
            $hojas[] = new KpisSheet($this->inicio, $this->fin);

        if (in_array('estados', $this->secciones))
            $hojas[] = new EstadosSheet($this->inicio, $this->fin);

        if (in_array('especialidades', $this->secciones))
            $hojas[] = new EspecialidadesSheet($this->inicio, $this->fin);

        if (in_array('seguros', $this->secciones))
            $hojas[] = new SegurosSheet($this->inicio, $this->fin);

        if (in_array('alertas', $this->secciones))
            $hojas[] = new AlertasFugaSheet($this->inicio, $this->fin);

        return $hojas;
    }
}
