<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $table = 'people';

    public function subjects(){
        return $this->belongsToMany(Subject::class, 'given_subjects','person_id','subject_id');
    }

}
