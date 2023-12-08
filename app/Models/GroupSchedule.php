<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSchedule extends Model 
{
    use HasFactory;
    protected $table = 'group_schedule';
    protected $guarded = false;
    public $timestamps = false;

    public function schedules()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'id', 'group_id');
    }
}