<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Discipline;
use App\Models\Classroom;
use Illuminate\Support\Facades\Log;

class ScheduleService 
{
    public static function addSchedule(array $attributes): Schedule
    {
        $disciplineName = Discipline::find($attributes['discipline_id'])->nameDiscipline ?? 'Unknown';
        $classroomName = Classroom::find($attributes['classroom_id'])->numberClassroom ?? 'Unknown';

        Log::info('[ScheduleService] Action: Adding schedule', [
            'day' => $attributes['day'],
            'week' => $attributes['week'],
            'class' => $attributes['class'],
            'discipline_id' => $attributes['discipline_id'],
            'discipline_name' => $disciplineName,
            'classroom_id' => $attributes['classroom_id'],
            'classroom_name' => $classroomName,
        ]);

        $schedule = Schedule::firstOrCreate([
            'day' => $attributes['day'],
            'week' => $attributes['week'],
            'class' => $attributes['class'],
            'discipline_id' => $attributes['discipline_id'],
            'classroom_id' => $attributes['classroom_id'],            
        ], [
            'type_discipline_id' => $attributes['type_discipline_id'],
        ]);
        Log::info('[ScheduleService] Action: Schedule added', ['schedule_id' => $schedule->id]);
        return $schedule;
    }
}