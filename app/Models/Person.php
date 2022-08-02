<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;
    protected $appends = ['IdentityNamed'];

    protected $guarded=[
        'id',

    ];

    public function getIdentityNamedAttribute (){
        switch ($this->identity){
            case 0: return "إداري";
            case 1: return "طالب";
            case 2: return "مدرس";
            default: return "--";
        }
    }

    public function images(){
        return $this->hasMany(Image::class, 'person_id','id');
    }

    public function logs(){
        return $this->hasmany(Log::class, 'person_id','id');
    }



}
