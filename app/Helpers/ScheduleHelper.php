<?php

namespace App\Helpers;

use App\Models\ClassModel;
use App\Models\Schedule;
use App\Services\GetFromDatabase\ScheduleRepository;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Enumerable;

class ScheduleHelper 
{
    const FIRST_WEEK = 1;
    const SECOND_WEEK = 2;

    public static $days = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];

    public static $showGroups = false;

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
        $html = "<thead><tr><th colspan=\"2\">$name</th>";
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
            [self::thead($titles), self::tbody($schedules, $titles)], 
            '{thead}{tbody}'
        );
    }

    protected static function thead(array $titles = []) : string
    {
        $html = '<thead><tr><th colspan="3"></th>{rows}</tr></thead>';
        $titles = array_map(function(string $title) {
            return "<th>{$title}</th>";
        }, $titles);
        return str_replace('{rows}', implode('', $titles), $html);
    }

    protected static function tbody(Collection $schedules = null, array $titles = []) : string
    {
        $html = '<tbody>{rows}</tbody>';
        $groupedSchedules = $schedules->groupBy(['day', 'class', 'week']);
        $rows = "";
        $currentDay = 0;
        $classes = ClassModel::orderBy('id')->get()->groupBy('id')->toArray();
        $listClasses = collect($classes)->map(function($class, $key) {
            return $class[0]['start_time'] . ' - ' . $class[0]['end_time'];
        });
        foreach ($groupedSchedules as $day => $classSchedules) {
            foreach ($listClasses as $numClass => $time) {
                $array = $classSchedules[$numClass] ?? [];
                $rows .= self::addRow($titles, 
                            $array[self::FIRST_WEEK] ?? [], 
                            self::$showGroups, 
                            ['id' => $numClass, 'time' => $time], 
                            $day != $currentDay ? $day : 0
                        ) 
                        . self::addRow($titles, $array[self::SECOND_WEEK] ?? [], self::$showGroups);
                $currentDay = $day;
            }
            
        }
        return str_replace('{rows}', $rows, $html);
    }

    protected static function addRow(array $titles = [], $schedules = [], bool $showGroups = false, array $class = [], int $day = 0) : string
    {        
        $rowTemplate = '<tr>{day}{class}{schedules}</tr>';
        $dayTemplate = $day > 0 ? '<td rowspan="14">{nameDay}</td>' : '';
        $classTemplate = !empty($class) ? '<td rowspan="2">{numClass}</td><td rowspan="2">{timeClass}</td>' : '';
        
        $listTd = [];
        foreach ($titles as $title) {
            $listTd[$title] = '<td></td>';
        }
        foreach ($schedules as $schedule) {
                $groups = $schedule->getGroups->map(function($group, $key) {
                    return $group['nameGroup'];
                })->all();
                $disc = $schedule['content'];
                $groupText = "";
                if ($showGroups) {
                    $groupText = $groups ? ", " . implode(', ', $groups) : "";
                }
                
                foreach ($groups as $group) {
                    if (in_array($group, $titles)) {
                        $listTd[$group] = "<td>{$disc}{$groupText}</td>";
                    }
                }
        }
        
        $html = str_replace(
            ['{day}', '{class}', '{schedules}'], 
            [$dayTemplate, $classTemplate, implode('', $listTd)], 
            $rowTemplate
        );
        return str_replace(
            ['{nameDay}', '{numClass}', '{timeClass}'], 
            [
                self::getDay($day), $class['id'] ?? '', 
                $class['time'] ?? ''
            ], $html);
    }
}