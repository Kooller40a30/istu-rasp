<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';
    protected $guarded = false;
    public function getFaculty() {
        return $this->belongsTo(Faculty::class,'faculty_id','id');
    }

    public function schedules() {
        return $this->hasManyThrough(
            Schedule::class, 
            GroupSchedule::class, 
            'group_id', 
            'list_group_id', 
            'id', 
            'list_group_id'
        );
    }
}
