<?php

namespace App\Services\GetFromDatabase;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Репозиторий для работы с расписаниями.
 */
class GetSchedule
{
    /**
     * Получает отсортированные расписания для указанной недели.
     *
     * @param Builder|HasMany $relation Запрос или отношение, содержащее расписания.
     * @param int $week Номер недели.
     * @return Collection Отсортированная коллекция расписаний.
     */
    public static function getSortedSchedulesForWeek($relation, int $week): Collection
    {
        return $relation
            ->orderBy('class')
            ->orderBy('week')
            ->orderBy('day')
            ->where('week', '=', $week)
            ->get();
    }

    /**
     * Сортирует коллекцию расписаний по дню, классу и неделе.
     *
     * @param Collection $collection Коллекция расписаний.
     * @return Collection Отсортированная коллекция расписаний.
     */
    public static function sortSchedulesCollection(\Illuminate\Support\Collection $collection)
    {
        return $collection
            ->filter(function ($item) {
                return $item && isset($item->day, $item->class, $item->week);
            })
            ->sortBy([
                ['day', 'asc'],
                ['class', 'asc'],
                ['week', 'asc'],
            ])
            ->values();
    }    
}
