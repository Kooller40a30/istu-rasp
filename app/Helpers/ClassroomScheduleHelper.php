<?php

namespace App\Helpers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Services\ClassroomService;
use App\Services\GetFromDatabase\GetSchedule;

class ClassroomScheduleHelper extends ScheduleHelper
{
    public static $showGroups = true;

    public static $showTeachers = true;

    public static $showClassroom = false;

    static function conditionSection(): callable
    {
        return function(Schedule $schedule, array $titles) {
            $room = $schedule->getClassroom['numberClassroom'];
            return array_intersect([$room], $titles);
        };
    }

    public static function generateClassroomSchedules(Faculty $faculty = null, Department $department = null)
    {
        $classrooms = ClassroomService::filterClassrooms($department, $faculty);
    
        $titlesCollect = collect();
        $schedules = collect();
    
        foreach ($classrooms as $classroom) {
            $title = $classroom->numberClassroom;
            if ($title && !$titlesCollect->contains($title)) {
                $titlesCollect[] = $title;
            }
    
            foreach ($classroom->schedules ?? [] as $schedule) {
                if ($schedule) {
                    $schedules[] = $schedule;
                }
            }
        }
    
        if ($schedules->isEmpty()) {
            return '<p>Нет данных для отображения расписания.</p>';
        }
    
        $schedules = GetSchedule::sortSchedulesCollection($schedules);
    
        return self::generateSchedules($schedules, $titlesCollect->all());
    }
    
}