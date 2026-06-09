<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMahasiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nim' => ['required', 'string', 'max:30', 'unique:mahasiswa,nim'],
            'nama' => ['required', 'string', 'max:120'],
            'ipk' => ['required', 'numeric', 'min:0', 'max:4'],
            'kehadiran' => ['required', 'integer', 'min:0', 'max:100'],
            'sks_lulus' => ['required', 'integer', 'min:0', 'max:180'],
            'status_kerja' => ['required', 'in:Tidak Bekerja,Part Time,Full Time'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'status_kelulusan' => ['required', 'in:Lulus,Tidak Lulus'],
        ];
    }
}
