<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DoctorAvailability extends Model
{
    protected $fillable = [
        'doctor_id',
        'available_date',
        'start_time',
        'end_time',
        'serviceable_type',
        'serviceable_id',
    ];

    protected $dates = [
        'available_date',
        'start_time',
        'end_time',
    ];

    /**
     * Get the owning serviceable model (Exam or Speciality).
     */
    public function serviceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the doctor that owns the availability.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
