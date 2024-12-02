<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'estimated_time_in_minutes',
        'price'
    ];
    protected $dates = [
        'available_date',
        'start_time',
        'end_time',
        'deleted_at'
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
