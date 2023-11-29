<?php

namespace App\Helpers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Services\GetFromDatabase\ScheduleRepository;

class TeacherScheduleHelper extends ScheduleHelper
{
    public static $showGroups = true;

    static function conditionSection(): callable
    {
        return function(Schedule $schedule, array $titles) {
            $teacher = $schedule->getTeacher['nameTeacher'];
            return array_intersect([$teacher], $titles);
        };
    }

    public static function generateTeachersSchedule(Faculty $faculty, Department $department = null) : string
    {
        $departments = $faculty->departments;
        if ($department) {
            $departments = $departments->where('id', '=', $department['id']);
        }
        $departmentsTeachers = $departments->map(function($dep) {
            return $dep->teachers;
        });
        $titlesCollect = collect();
        $schedules = collect();
        foreach ($departmentsTeachers as $dep => $teachers) {
            foreach ($teachers as $teacher) {
                if (!$titlesCollect->contains($teacher['nameTeacher'])) {
                    $titlesCollect[] = $teacher['nameTeacher'];
                }
                foreach ($teacher->schedules as $schedule) {
                    $schedules[] = $schedule;
                }
            }
        }        
        $titles = $titlesCollect->all();        
        $schedules = ScheduleRepository::sortManySchedules($schedules);
        return self::generateSchedules($schedules, $titles);  
    }

}