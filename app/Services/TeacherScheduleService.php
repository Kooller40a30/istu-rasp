<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use Illuminate\Support\Facades\Log;

class TeacherScheduleService 
{
    public static function addTeacherSchedule(Teacher $teacher, Schedule $schedule): TeacherSchedule
    {
        Log::info('[TeacherScheduleService] Action: Adding teacher schedule', ['teacher_id' => $teacher['id'], 'schedule_id' => $schedule['id']]);
        $teacherSchedule = TeacherSchedule::firstOrCreate([
            'teacher_id' => $teacher['id'],
            'schedule_id' => $schedule['id'],
        ]);
        Log::info('[TeacherScheduleService] Action: Teacher schedule added', ['teacher_schedule_id' => $teacherSchedule->id]);
        return $teacherSchedule;
    }
}