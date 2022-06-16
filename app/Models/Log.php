<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    public function cam(){
        return $this->belongsTo(Cam::class, 'cam_id','id');
    }

}
