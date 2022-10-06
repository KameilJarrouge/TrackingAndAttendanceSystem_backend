<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Person
{
    use HasFactory;
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('professors', function (Builder $builder) {
            $builder->where('identity', '=', 2);
        });
    }

    protected $table = 'people';

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'given_subjects', 'person_id', 'subject_id');
    }

    public function givenSubjects()
    {
        return $this->hasMany(GivenSubject::class, 'person_id', 'id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'person_id', 'id');
    }
}
