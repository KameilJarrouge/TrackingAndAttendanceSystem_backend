<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GivenSubject extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected function attendancePre(): Attribute
    {

        return Attribute::make(function ($value) {
            if ($value === null) {
                return Setting::query()->first()->attendance_pre;
            }
            return $value;
        });
    }
    protected function attendancePost(): Attribute
    {

        return Attribute::make(function ($value) {
            if ($value === null) {
                return Setting::query()->first()->attendance_post;
            }
            return $value;
        });
    }
    protected function attendancePresent(): Attribute
    {

        return Attribute::make(function ($value) {
            if ($value === null) {
                return Setting::query()->first()->attendance_present;
            }
            return $value;
        });
    }
    protected function attendanceExtend(): Attribute
    {

        return Attribute::make(function ($value) {
            if ($value === null) {
                return 0;
            }
            return $value;
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'person_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(ProfAttendance::class, 'given_subject_id', 'id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }

    public function cam()
    {
        return $this->belongsTo(Cam::class, 'cam_id', 'id');
    }

    public function takenSubjects()
    {
        $foreignId = "given_subject_id_th";
        if ($this->is_thoery !== 1) {
            $foreignId = "given_subject_id_pr";
        }
        // return $foreignId;
        return $this->hasMany(TakenSubject::class, $foreignId, 'id');
    }
    public function takenSubjectsPr()
    {

        return $this->hasMany(TakenSubject::class, "given_subject_id_pr", 'id');
    }
    public function takenSubjectsTh()
    {

        return $this->hasMany(TakenSubject::class, "given_subject_id_th", 'id');
    }

    public function activeWeekAttendance()
    {
        $foreignId = "given_subject_id_th";
        if (!$this->is_theory === 1) {
            $foreignId = "given_subject_id_pr";
        }
        $currentSemester = Semester::getLatest();
        $weekNumber = Carbon::parse($currentSemester->semester_start)->diffInWeeks(Carbon::now()) + 1;
        return $this->hasManyThrough(StdAttendance::class, TakenSubject::class, $foreignId, 'taken_subject_id', 'id', 'id')->where('std_attendances.week', $weekNumber)->where('std_attendances.theory', $this->is_theory);
    }

    public function activeWeekAttendanceAttended()
    {
        return $this->activeWeekAttendance()->where('attended', 1);
    }

    public function activeWeekProfAttendanceAttended()
    {
        $currentSemester = Semester::getLatest();
        $weekNumber = Carbon::parse($currentSemester->semester_start)->diffInWeeks(Carbon::now()) + 1;
        $this->professorAttended = $this->attendances()->where('week', $weekNumber)->first('attended');
    }
}
