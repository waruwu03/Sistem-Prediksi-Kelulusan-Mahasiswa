<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictionHistory extends Model
{
    protected $fillable = [
        'user_id',
        'nim',
        'nama_mahasiswa',
        'algorithm',
        'input_data',
        'predicted_status',
        'probability_lulus',
        'probability_tidak_lulus',
        'confidence',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'input_data' => 'array',
            'probability_lulus' => 'float',
            'probability_tidak_lulus' => 'float',
            'confidence' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
