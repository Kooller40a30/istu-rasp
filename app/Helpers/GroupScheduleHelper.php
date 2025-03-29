<?php

namespace App\Helpers;

use App\Models\Course;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Schedule;
use App\Services\GetFromDatabase\GetSchedule;

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

    public static function generateCourseSchedule(Faculty $faculty = null, Course $course = null): string
    {
        if ($faculty instanceof Faculty) {
            $groupsModel = $faculty->groups;
        } else {
            $groupsModel = Group::all();
        }
    
        if ($course instanceof Course) {
            $groupsModel = $groupsModel->where('course_id', $course->id);
        }
    
        if ($groupsModel->isEmpty()) {
            return '<p>Нет данных для отображения расписания.</p>';
        }
    
        $titles = $groupsModel->map(fn($group) => $group->nameGroup)->all();
    
        $schedules = collect();
        foreach ($groupsModel as $group) {
            foreach ($group->schedules ?? [] as $schedule) {
                if ($schedule) {
                    $schedules[] = $schedule;
                }
            }
        }
    
        $schedules = GetSchedule::sortSchedulesCollection($schedules);
    
        return self::generateSchedules($schedules, $titles);
    }
    
}