<?php

namespace App\Services\GetFromDatabase;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Log;

/**
 * Репозиторий для работы с расписаниями.
 */
class GetSchedule
{
    /**
     * Получает отсортированные расписания для указанной недели.
     *
     * @param Builder|HasMany|HasManyThrough $relation Запрос или отношение, содержащее расписания.
     * @param int $week Номер недели.
     * @return Collection Отсортированная коллекция расписаний.
     */
    public static function getSortedSchedulesForWeek(Builder|HasMany|HasManyThrough $relation, int $week): Collection
    {
        Log::info('[GetSchedule] Action: Getting sorted schedules for week', ['week' => $week]);
        $result = $relation
            ->orderBy('class')
            ->orderBy('week')
            ->orderBy('day')
            ->where('week', '=', $week)
            ->get();
        Log::info('[GetSchedule] Action: Retrieved sorted schedules', ['count' => $result->count()]);
        return $result;
    }

    /**
     * Сортирует коллекцию расписаний по дню, классу и неделе.
     *
     * @param \Illuminate\Support\Collection $collection Коллекция расписаний (может содержать не только модели Schedule).
     * @return \Illuminate\Support\Collection Отсортированная коллекция расписаний.
     */
    public static function sortSchedulesCollection(\Illuminate\Support\Collection $collection): \Illuminate\Support\Collection
    {
        Log::info('[GetSchedule] Action: Sorting schedules collection', ['initial_count' => $collection->count()]);
        $sorted = $collection
            ->filter(function ($item) {
                return is_object($item) && isset($item->day, $item->class, $item->week)
                    || is_array($item) && isset($item['day'], $item['class'], $item['week']);
            })
            ->sortBy([
                ['day', 'asc'],
                ['class', 'asc'],
                ['week', 'asc'],
            ])
            ->values();
        Log::info('[GetSchedule] Action: Sorted schedules collection', ['sorted_count' => $sorted->count()]);
        return $sorted;
    }    
}
