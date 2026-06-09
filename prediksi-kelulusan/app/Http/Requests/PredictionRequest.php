<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PredictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nim' => ['nullable', 'string'],
            'nama_mahasiswa' => ['nullable', 'string'],
            'ipk' => ['required', 'numeric', 'min:0', 'max:4'],
            'kehadiran' => ['required', 'integer', 'min:0', 'max:100'],
            'sks_lulus' => ['required', 'integer', 'min:0', 'max:180'],
            'status_kerja' => ['required', 'in:Tidak Bekerja,Part Time,Full Time'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'algorithm' => ['required', 'in:naive_bayes,knn,decision_tree,neural_network'],
        ];
    }
}
