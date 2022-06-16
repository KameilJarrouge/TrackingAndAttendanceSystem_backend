<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakenSubject extends Model
{
    use HasFactory;

    protected $guarded= [
        'id',
    ];

    public function student(){
        return $this->belongsTo(Student::class, 'person_id','id');
    }

    public function subject(){
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function attendances(){
        return $this->hasMany(StdAttendance::class,'taken_subject_id','id');
    }

    public function thSubjectGiven(){
        return $this->belongsTo(GivenSubject::class,'given_subject_id_th','id');
    }

    public function prSubjectGiven(){
        return $this->belongsTo(GivenSubject::class,'given_subject_id_pr','id');
    }

}
