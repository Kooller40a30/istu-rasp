<?php

namespace App\Helpers;

use App\Models\Group;

class ScheduleHelper 
{
    public static function generateGroupSchedule(Group $group)
    {
        $schedules = $group->schedules();
        $html = "";
        dd($schedules);
        // foreach ($schedules as $schedule) {
        //     $html .= $schedule->;
        // }
        return $html;
    }
}