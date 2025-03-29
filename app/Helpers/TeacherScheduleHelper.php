<?php

namespace App\Helpers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Services\GetFromDatabase\GetSchedule;

class TeacherScheduleHelper extends ScheduleHelper
{
    public static $showGroups = true;

    static function conditionSection(): callable
    {
        return function(Schedule $schedule, array $titles) {
            $teachers = $schedule->getTeachers->map(function($teacher) {
                return $teacher['shortNameTeacher'];
            })->all();
            return array_intersect($teachers, $titles);
        };
    }

    public static function generateTeachersSchedule(Faculty $faculty = null, Department $department = null) : string
    {
        if ($faculty instanceof Faculty) {
            $departments = $faculty->departments;
        } else {
            $departments = Department::all();
        }
    
        if ($department instanceof Department) {
            $departments = $departments->where('id', $department->id);
        }
    
        $titlesCollect = collect();
        $schedules = collect();
    
        foreach ($departments as $dep) {
            foreach ($dep->teachers as $teacher) {
                $name = $teacher->shortNameTeacher;
                if ($name && !$titlesCollect->contains($name)) {
                    $titlesCollect[] = $name;
                }
    
                foreach ($teacher->schedules ?? [] as $schedule) {
                    if ($schedule) {
                        $schedules[] = $schedule;
                    }
                }
            }
        }
    
        if ($schedules->isEmpty()) {
            return '<p>Нет расписаний для отображения.</p>';
        }
    
        $schedules = GetSchedule::sortSchedulesCollection($schedules);
    
        return self::generateSchedules($schedules, $titlesCollect->all());
    }    

}