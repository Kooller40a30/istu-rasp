<?php

namespace App\Helpers;

use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Group;
use App\Services\GetFromDatabase\ScheduleRepository;

class GroupScheduleHelper extends ScheduleHelper
{
    public static function generateCourseSchedule(Faculty $faculty, Course $course = null) : string
    {
        $groupsModel = $faculty->groups;
        if ($course) {
            $groupsModel = $groupsModel->where('course_id', '=', $course['id']);
        }
        $groupsModel = ScheduleRepository::sortManySchedules($groupsModel);
        dd($groupsModel);
        $titles = $groupsModel->map(function($item, $key) {
            return $item['nameGroup'];
        })->all();
        $groupsSchedules = $groupsModel->map(function($group, $key) {            
            return $group->schedules;
        });
        return self::generateSchedules($groupsSchedules, $titles);        
    }
}