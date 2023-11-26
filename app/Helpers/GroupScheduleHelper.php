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
        $html = "";
        $groupsModel = $faculty->groups();
        if ($course) {
            $groupsModel->where('course_id', '=', $course['id']);
        }
        $groups = $groupsModel->get();
        foreach ($groups as $group) {
            $html .= self::generateSchedule($group->schedules(), $group['nameGroup']);
        }
        return $html;
    }
}