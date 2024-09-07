<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discipline extends Model 
{
    use HasFactory;
    protected $table = 'discipline';
    public $timestamps = false;
    protected $guarded = false;

    public function schedules() 
    {
        return $this->hasMany(Schedule::class, 'discipline_id', 'id');
    }
}