<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeDiscipline extends Model 
{
    use HasFactory;
    protected $table = 'type_discipline';
    protected $guarded = false;
    public $timestamps = false;

    public function schedules() 
    {
        return $this->hasMany(Schedule::class, 'type_discipline', 'id');
    }
}