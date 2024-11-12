<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_availability_id',
        'patient_id',
        'start_time',
        'end_time',
        'status'
    ];
    /**
     * Define relationship with DoctorAvailability.
     */
    public function doctorAvailability()
    {
        return $this->belongsTo(DoctorAvailability::class);
    }

    /**
     * Define relationship with Patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Check if consultation is active (not canceled).
     */
    public function isActive()
    {
        return $this->status !== 'canceled';
    }

    /**
     * Scope for retrieving active consultations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'canceled');
    }
}
