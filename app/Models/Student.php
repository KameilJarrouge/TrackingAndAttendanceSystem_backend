<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Person
{
    use HasFactory;

    protected $table = 'people';

    public function subjects(){
        $this->belongsToMany(Subject::class, 'taken_subjects', 'person_id','subject_id');
    }


}
