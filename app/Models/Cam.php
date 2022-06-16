<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cam extends Model
{
    use HasFactory;

    protected $guarded= [
        'id',
    ];

    public function givenSubjects(){
        return $this->hasMany(GivenSubject::class, 'cam_id','id');
    }

    public function schedule(){
        return $this->hasMany(Schedule::class, 'cam_id','id');
    }

    public function log(){
        return $this->hasMany(Log::class, 'cam_id', 'id');
    }

}
