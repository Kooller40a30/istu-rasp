<?php

namespace App\Services\GetFromDatabase;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleRepository 
{
    public static function sortSchedules(HasMany $relation, int $week)
    {
        return $relation->select('class', 'week', 'day', 'content', 'group_id')
            ->orderBy('class')
            ->orderBy('week')
            ->orderBy('day')
            ->where('week', '=', $week)
            ->groupBy('class', 'week', 'day', 'content', 'group_id')
            ->get();
    }
}