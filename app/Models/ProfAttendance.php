<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfAttendance extends Model
{
    use HasFactory;

    protected $guarded=[
        'id',
    ];

    public function givenSubject(){
        return $this->belongsTo(GivenSubject::class, 'given_subject_id','id');
    }
}
