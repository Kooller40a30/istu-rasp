<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\Schedule;

class GroupScheduleService 
{
    public static function addGroupSchedule(Group $group, Schedule $schedule)
    {
        return GroupSchedule::firstOrCreate([
            'group_id' => $group['id'],
            'schedule_id' => $schedule['id'],
        ]);
    }
}