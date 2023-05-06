<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StdAttendance extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function takenSubject(){
        return $this->belongsTo(TakenSubject::class, 'taken_subject_id','id');
    }
}
