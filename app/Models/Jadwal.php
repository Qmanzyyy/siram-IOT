<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    /** @use HasFactory<\Database\Factories\JadwalFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jadwal';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'waktu_aktif_pertama',
        'waktu_aktif_kedua',
        'lama_operasi',
        'aktif',
        'hari',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'waktu_aktif_pertama' => 'datetime:H:i',
        'waktu_aktif_kedua' => 'datetime:H:i',
        'aktif' => 'boolean',
        'hari' => 'array',
    ];
}
