<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'diagnostico_rep'   => 'nullable|string|max:1000',
            'tipo_rep'          => 'required|in:Consulta,Reconsulta_0_7,Reconsulta_8_15,Campana',
            'paciente_asistio'  => 'required|boolean',
            'motivo_no_asistio' => 'required_if:paciente_asistio,0|nullable|in:Cancelo_por_telefono,No_se_presento_sin_aviso,Derivado_consulta_privada,Otro',
            'motivo_otro_rep'   => 'required_if:motivo_no_asistio,Otro|nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_rep.required'          => 'Debes seleccionar el tipo de consulta.',
            'paciente_asistio.required'  => 'Debes indicar si el paciente se presentó.',
            'motivo_no_asistio.required_if' => 'Debes seleccionar el motivo de ausencia.',
            'motivo_otro_rep.required_if'   => 'Describe el motivo en el campo de texto.',
        ];
    }
}
