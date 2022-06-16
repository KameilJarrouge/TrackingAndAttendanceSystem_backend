<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GivenSubject extends Model
{
    use HasFactory;

    protected $guarded= [
        'id',
    ];

    public function professor(){
        return $this->belongsTo(Professor::class, 'person_id','id');
    }

    public function subject(){
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function attendances(){
        return $this->hasMany(ProfAttendance::class,'given_subject_id','id');
    }

    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id','id');
    }
}
