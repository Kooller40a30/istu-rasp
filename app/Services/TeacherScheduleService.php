<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\TeacherSchedule;

class TeacherScheduleService 
{
    public static function addTeacherSchedule(Teacher $teacher, Schedule $schedule)
    {
        return TeacherSchedule::firstOrCreate([
            'teacher_id' => $teacher['id'],
            'schedule_id' => $schedule['id'],
        ]);
    }
}