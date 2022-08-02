<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function activeWeekTh(){
        $currentSemester = Semester::getLatest();
        $weekNumber = Carbon::parse($currentSemester->semester_start)->diffInWeeks(Carbon::now());
        return $this->attendancesTh()->where('week',$weekNumber);
    }

    public function activeWeekPr(){
        $currentSemester = Semester::getLatest();
        $weekNumber = Carbon::parse($currentSemester->semester_start)->diffInWeeks(Carbon::now());
        return $this->attendancesPr()->where('week',$weekNumber);
    }


    public function attendances(){
        return $this->hasMany(StdAttendance::class,'taken_subject_id','id');
    }

    public function attendancesTh(){
        return $this->hasMany(StdAttendance::class,'taken_subject_id','id')->where('theory',1);
    }
    public function attendancesPr(){
        return $this->hasMany(StdAttendance::class,'taken_subject_id','id')->where('theory',0);
    }

    public function thSubjectGiven(){
        return $this->belongsTo(GivenSubject::class,'given_subject_id_th','id');
    }

    public function prSubjectGiven(){
        return $this->belongsTo(GivenSubject::class,'given_subject_id_pr','id');
    }

    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id','id');
    }

}
