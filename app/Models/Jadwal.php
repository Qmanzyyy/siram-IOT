<?php

namespace App\Models;

use Database\Factories\JadwalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    /** @use HasFactory<JadwalFactory> */
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
        'run_now',
        'last_run_at',
        'hari',
        'automation_flow',
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
        'run_now' => 'boolean',
        'last_run_at' => 'datetime',
        'hari' => 'array',
        'automation_flow' => 'array',
    ];
}
