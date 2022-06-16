<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $guarded=[
        'id'
    ];

    public function givenSubjects(){
        return $this->hasMany(GivenSubject::class,'semester_id','id');
    }

    public function takenSubjects(){
        return $this->hasMany(TakenSubject::class,'semester_id','id');
    }

}
