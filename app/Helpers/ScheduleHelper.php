<?php

namespace App\Helpers;

use App\Models\ClassModel;
use App\Models\Schedule;
use App\Services\GetFromDatabase\ScheduleRepository;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleHelper 
{
    const FIRST_WEEK = 1;
    const SECOND_WEEK = 2;

    public static $days = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];

    protected static function createSubrowSchedule($schedules, bool $showGroup = false)
    {
        $days = range(1, 6);
        $listTd = [];
        foreach ($days as $day) {
            $listTd[$day] = '<td></td>';
        }
        foreach ($schedules as $schedule) {
            if (in_array($schedule['day'], $days)) {
                $disc = $schedule['content'];
                $groupText = "";
                if ($showGroup) {
                    $groups = $schedule->getGroups->map(function($group, $key) {
                        return $group['nameGroup'];
                    })->all();
                    $groupText = $groups ? ", " . implode(', ', $groups) : "";
                }
                $listTd[$schedule['day']] = "<td>{$disc}{$groupText}</td>";  
            }          
        }
        return implode('', $listTd);
    }

    public static function getDay(int $day) 
    {
        switch ($day) {
            case 1: return 'Пн';
            case 2: return 'Вт';
            case 3: return 'Ср';
            case 4: return 'Чт';
            case 5: return 'Пт';
            case 6: return 'Сб';
            default: return 'Вс';
        }
    }

    protected static function createHeaderTable(string $name) 
    {
        $html = "<thead style=\"position: sticky; top: 0;\"><tr><th colspan=\"2\">$name</th>";
        foreach (self::$days as $day) {
            $html .= "<th>$day</th>";
        }
        return $html . '</tr></thead>';
    }

    public static function generateSchedule($schedules, string $name, bool $showGroup = false) : string
    {
        $firstSchedules = ScheduleRepository::sortSchedules(clone $schedules, self::FIRST_WEEK)->all();
        $secondSchedules = ScheduleRepository::sortSchedules(clone $schedules, self::SECOND_WEEK)->all();
        $html = self::createHeaderTable($name) . '<tbody>';        
        $classes = ClassModel::orderBy('id')->get();
        foreach ($classes as $class) {
            $firstSchedule = array_filter($firstSchedules, function($schedule) use ($class) {
                return $schedule['class'] === $class['id'];
            });
            $secondSchedule = array_filter($secondSchedules, function($schedule) use ($class) {
                return $schedule['class'] === $class['id'];
            });
            $htmlFirstSchedule = self::createSubrowSchedule($firstSchedule, $showGroup);
            $htmlSecondSchedule = self::createSubrowSchedule($secondSchedule, $showGroup);
            $numLesson = $class['id'];
            $timeLesson = $class['start_time'] . ' - ' . $class['end_time'];

            $html .= "<tr>
                <td rowspan=\"2\">$numLesson</td>
                <td rowspan=\"2\">$timeLesson</td>
                $htmlFirstSchedule
            </tr>
            <tr>$htmlSecondSchedule</tr>";
        }
        return $html . '</tbody>';
    }

    public static function generateSchedules(Collection $schedules = null, array $titles = []) : string
    {
        return str_replace(
            ['{thead}', '{tbody}'], 
            [self::thead($titles), self::tbody($schedules)], 
            "{thead}{tbody}"
        );
    }

    protected static function thead(array $titles = []) : string
    {
        $html = "<thead><tr><td colspan=\"3\"></td>{rows}</tr></thead>";
        $titles = array_map(function(string $title) {
            return "<th>{$title}</th>";
        }, $titles);
        return str_replace('{rows}', implode('', $titles), $html);
    }

    protected static function tbody(Collection $schedules = null) : string
    {
        $html = "<tbody>{rows}</tbody>";
        $rows = [];
        self::rows();
        foreach($schedules as $schedule) {

        }
        return str_replace('{rows}', implode('', $rows), $html);
    }

    protected static function rows() : array
    {
        $classes = ClassModel::orderBy('id')->get();
        // dd($classes);
        foreach (self::$days as $day) {

        }
        foreach ($classes as $class) {

        }
        return [];
    }
}