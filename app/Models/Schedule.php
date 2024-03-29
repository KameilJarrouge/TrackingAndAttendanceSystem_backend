<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded=[
        'id',
    ];

    public function cam(){
        return $this->belongsTo(Cam::class, 'cam_id','id');
    }
}
