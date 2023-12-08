<?php

namespace App\Helpers;

use App\Models\Course;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Services\GetFromDatabase\ScheduleRepository;

class GroupScheduleHelper extends ScheduleHelper
{
    public static $showTeachers = true;
    
    static function conditionSection(): callable
    {
        return function(Schedule $schedule, array $titles) {
            $groups = $schedule->getGroups->map(function($group) {
                return $group['nameGroup'];
            })->intersect($titles);
            return $groups;
        };
    }

    public static function generateCourseSchedule(Faculty $faculty, Course $course = null) : string
    {
        $groupsModel = $faculty->groups;
        if ($course) {
            $groupsModel = $groupsModel->where('course_id', '=', $course['id']);
        }
        $titles = $groupsModel->map(function($item, $key) {
            return $item['nameGroup'];
        })->all();
        $groupsSchedules = $groupsModel->map(function($group) {            
            return $group->schedules;
        });
        $schedules = collect();
        foreach ($groupsSchedules as $groupSchedules) {
            foreach ($groupSchedules as $schedule) {
                $schedules[] = $schedule;
            }
        }
        $schedules = ScheduleRepository::sortManySchedules($schedules);
        return self::generateSchedules($schedules, $titles);        
    }
}