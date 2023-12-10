<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    const NOT_VALID_ID = -1;
    
    protected $table = 'faculties';

    protected $guarded = false;

    public function groups(){
        return $this->hasMany(Group::class);
    }

    public function departments(){
        return $this->hasMany(Department::class);
    }

    public function classrooms(){
        return $this->hasMany(Classroom::class);
    }
}
