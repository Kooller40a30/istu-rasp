<?php

namespace App\Helpers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Services\ClassroomService;
use App\Services\GetFromDatabase\ScheduleRepository;

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
            if (!$titlesCollect->contains($classroom['numberClassroom'])) {
                $titlesCollect[] = $classroom['numberClassroom'];
            }
            foreach ($classroom->schedules as $schedule) {
                $schedules[] = $schedule;
            }
        }
        $titles = $titlesCollect->all();        
        $schedules = ScheduleRepository::sortManySchedules($schedules);
        return self::generateSchedules($schedules, $titles); 
    }
}