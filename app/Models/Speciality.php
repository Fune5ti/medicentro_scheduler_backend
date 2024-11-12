<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{


    protected $fillable = [
        'name',
        'estimated_time_in_minutes'
    ];

    /**
     * The doctors that belong to the speciality.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class);
    }
    public function availabilities()
    {
        return $this->morphMany(DoctorAvailability::class, 'serviceable');
    }

}
