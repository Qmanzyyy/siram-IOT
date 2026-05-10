<?php

namespace App\Models;

use Database\Factories\DeviceControlFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceControl extends Model
{
    /** @use HasFactory<DeviceControlFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'device_type',
        'name',
        'mode',
        'calibration_max_steps',
        'calibration_percentage',
        'speed',
        'servo_angle',
        'relay_state',
        'current_position',
        'is_active',
        'last_heartbeat',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'calibration_percentage' => 'decimal:2',
        'relay_state' => 'boolean',
        'is_active' => 'boolean',
        'last_heartbeat' => 'datetime',
    ];

    /**
     * Check if device is a motor (dinamo).
     */
    public function isMotor(): bool
    {
        return in_array($this->device_type, ['dinamo_x', 'dinamo_y']);
    }

    /**
     * Check if device is relay.
     */
    public function isRelay(): bool
    {
        return $this->device_type === 'relay_pump';
    }

    /**
     * Check if device is servo.
     */
    public function isServo(): bool
    {
        return $this->device_type === 'servo_nozzle';
    }
}
