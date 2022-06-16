<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $guarded=[
        'id',
        'fri',
        'sat',
        'sun',
        'mon',
        'tue',
        'wed',
        'thu'
    ];

    public function images(){
        return $this->hasMany(Image::class, 'person_id','id');
    }



}
