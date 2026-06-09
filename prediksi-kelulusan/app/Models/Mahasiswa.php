<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'nim',
        'nama',
        'ipk',
        'kehadiran',
        'sks_lulus',
        'status_kerja',
        'jenis_kelamin',
        'status_kelulusan',
    ];

    protected function casts(): array
    {
        return [
            'ipk' => 'float',
            'kehadiran' => 'integer',
            'sks_lulus' => 'integer',
        ];
    }
}
