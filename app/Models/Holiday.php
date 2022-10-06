<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function scopeCurrent($query)
    {
        return $query->where('start', '<=', Carbon::now()->format('Y-m-d'))->where('end', '>=', Carbon::now()->format('Y-m-d'));
    }
    public function scopeUpcoming($query)
    {
        return $query->where('start', '>', Carbon::now()->format('Y-m-d'));
    }
    public function scopePassed($query)
    {
        return $query->where('end', '<', Carbon::now()->format('Y-m-d'));
    }
}
