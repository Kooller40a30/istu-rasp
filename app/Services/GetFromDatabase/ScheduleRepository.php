<?php

namespace App\Services\GetFromDatabase;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ScheduleRepository 
{
    public static function sortSchedules($relation, int $week)
    {
        return $relation
            ->orderBy('class')
            ->orderBy('week')
            ->orderBy('day')
            ->where('week', '=', $week)
            ->get();
    }

    public static function sortManySchedules(Collection $collection)
    {
        return $collection->sortBy([
            ['day', 'asc'],
            ['class', 'asc'],
            ['week', 'asc'],
        ]);
    }
}