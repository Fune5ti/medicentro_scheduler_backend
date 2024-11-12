<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'name',
        'description',
        'estimated_time_in_minutes'
    ];
    protected $dates = [
        'available_date',
        'start_time',
        'end_time',
    ];

    /**
     * The doctors that are associated with the exam.
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_exams');
    }
    public function availabilities()
    {
        return $this->morphMany(DoctorAvailability::class, 'serviceable');
    }

}
