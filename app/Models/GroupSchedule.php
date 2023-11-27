<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSchedule extends Model 
{
    use HasFactory;
    protected $table = 'group_schedule';
    protected $guarded = false;

    public function schedules()
    {
        return $this->hasOne(Schedule::class, 'list_group_id', 'list_group_id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'id', 'group_id');
    }
}