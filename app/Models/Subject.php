<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $guarded =[
        'id'
    ];


    public function students(){
        return $this->belongsToMany(Student::class,'taken_subjects','subject_id','person_id');
    }

    public function takenSubjects(){
        return $this->hasMany(TakenSubject::class, 'subject_id','id');
    }

    public function professors(){
        return $this->belongsToMany(Professor::class, 'given_subjects','subject_id','person_id');
    }

    public function givenSubjects(){
        return $this->hasMany(GivenSubject::class,'subject_id','id');
    }



}
