<?php

namespace App\Services;

use App\Models\Schedule;

class ScheduleService 
{
    public static function addSchedule(array $attributes) 
    {
        return Schedule::firstOrCreate([
            'day' => $attributes['day'],
            'week' => $attributes['week'],
            'class' => $attributes['class'],
            'discipline_id' => $attributes['discipline_id'],
            'classroom_id' => $attributes['classroom_id'],            
        ], [
            'type_discipline_id' => $attributes['type_discipline_id'],
        ]);
    }
}