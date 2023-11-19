<?php

namespace App\Services\GetFromDatabase;

use App\Models\Group;

class ScheduleRepository 
{
    public static function findSchedules(Group $group, int $week) 
    {
        return $group->schedules()
            ->select('class', 'week', 'day', 'content')
            ->orderBy('class')
            ->orderBy('week')
            ->orderBy('day')
            ->where('week', '=', $week)
            ->groupBy('class', 'week', 'day', 'content')
            ->get();
    }
}