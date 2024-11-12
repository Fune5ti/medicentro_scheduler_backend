<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'crm',
        'phone',
        'email',
        'photo_location',
    ];


    public function specialities()
    {
        return $this->belongsToMany(Speciality::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'doctor_exams');
    }

    public function availabilities()
    {
        return $this->hasMany(DoctorAvailability::class);
    }

}
