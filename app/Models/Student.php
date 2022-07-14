<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Person
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('students', function (Builder $builder) {
            $builder->where('identity', '=',1);
        });
    }

    protected $table = 'people';

    public function subjects(){
        return $this->belongsToMany(Subject::class, 'taken_subjects', 'person_id','subject_id');
    }


}
