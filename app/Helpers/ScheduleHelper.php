<?php

namespace App\Helpers;

use App\Models\ClassModel;
use App\Models\Schedule;
use App\Services\GetFromDatabase\ScheduleRepository;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Enumerable;

abstract class ScheduleHelper 
{
    /**
     * Неделя над чертой
     * @var integer
     */
    const FIRST_WEEK = 1;

    /**
     * Неделя под чертой
     * @var integer
     */
    const SECOND_WEEK = 2;

    /**
     * Дни недели
     * @var array
     */
    public static $days = [
        1 => 'Пн', 2 => 'Вт', 
        3 => 'Ср', 4 => 'Чт', 
        5 => 'Пт', 6 => 'Сб'
    ];

    /**
     * Показывать список групп в расписании
     * @var boolean
     */
    public static $showGroups = false;

    /**
     * Показать список преподавателей в расписании
     *
     * @var boolean
     */
    public static $showTeachers = false;

    /**
     * Показать аудиторию
     *
     * @var boolean
     */
    public static $showClassroom = true;

    /**
     * Условие добавления расписания к заголовку
     * @return callable function(Collection $schedules, array $titles) : bool
     */
    abstract static function conditionSection() : callable;

    /**
     * Сформировать строку расписания
     * @param Schedules[] $schedules расписание
     * @param array $titles список заголовков, к которым прикрепляется расписание
     * @param callable $condition условие добавления расписания к заголовку
     * @return void
     */
    protected static function createSubrowSchedule(Collection $schedules, array $titles, callable $condition) : string
    {
        $listTd = [];
        foreach ($titles as $title) {
            $listTd[$title] = '<td></td>';
        }
        foreach ($schedules as $schedule) { 
            // if (!$schedule->getDiscipline) {
            //     continue;
            // }
            $disc = $schedule->getDiscipline['nameDiscipline'];
            $groupText = "";
            if (static::$showGroups) {
                $groups = $schedule->getGroups->map(function($group, $key) {
                    return $group['nameGroup'];
                })->all();
                $groupText = $groups ? ", " . implode(', ', $groups) : "";
            }
            $teacherText = "";
            if (static::$showTeachers) {
                // dd($schedule, $schedule->getTeachers);
                // @todo фигня с преподами, косяк в БД?
                $teachers = $schedule->getTeachers->map(function($teacher, $key) {
                    return $teacher['shortNameTeacher'];
                })->all();
                $teacherText = $teachers ? ", " . implode(', ', $teachers) : "";
            }
            $room = "";
            if (static::$showClassroom && $schedule->getClassroom) {
                $room = ", " . $schedule->getClassroom['numberClassroom'];
            }
            $type = $schedule->getTypeDiscipline['shortName'];
            $validKeys = $condition($schedule, $titles);
            foreach ($validKeys as $key) {
                $listTd[$key] = "<td>({$type}) {$disc}{$teacherText}{$groupText}{$room}</td>";
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

    public static function generateSchedule($schedules, string $name) : string
    {
        $countRowsDays = 6;
        $firstSchedules = ScheduleRepository::sortSchedules(clone $schedules, self::FIRST_WEEK);
        $secondSchedules = ScheduleRepository::sortSchedules(clone $schedules, self::SECOND_WEEK);
        $html = self::colgroup(false, $countRowsDays);
        $html .= self::createHeaderTable($name) . '<tbody>';        
        $classes = ClassModel::orderBy('id')->get();
        foreach ($classes as $class) {
            $filter = function($schedule) use ($class) {
                return $schedule['class'] === $class['id'];
            };
            $firstSchedule = $firstSchedules->filter($filter);
            $secondSchedule = $secondSchedules->filter($filter);
            $dayKeys = array_keys(self::$days);
            $funcAddSchedule = function($schedule, $day) {
                return [$schedule['day']];
            };
            $htmlFirstSchedule = self::createSubrowSchedule($firstSchedule, $dayKeys, $funcAddSchedule);
            $htmlSecondSchedule = self::createSubrowSchedule($secondSchedule, $dayKeys, $funcAddSchedule);
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
        $rows = count($titles);
        return str_replace(
            ['{colgroup}', '{thead}', '{tbody}'], 
            [self::colgroup(true, $rows), self::thead($titles), self::tbody($schedules, $titles)], 
            '{colgroup}{thead}{tbody}'
        );
    }

    protected static function colgroup(bool $showDayCol = false, int $countCols = 0)
    {
        $html = "<colgroup>{dayCol}{classCol}{timeCol}{cols}</colgroup>";
        $dayTemplate = $showDayCol ? '<col class="day"></col>' : '';
        $classTemplate = '<col class="class"></col>';
        $timeTemplate = '<col class="time"></col>';
        return str_replace(
            ['{dayCol}', '{classCol}', '{timeCol}', '{cols}'], 
            [$dayTemplate, $classTemplate, $timeTemplate, str_repeat('<col></col>', $countCols)],
            $html);
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
        $funcAddSchedule = static::conditionSection();
        foreach ($groupedSchedules as $day => $classSchedules) {
            foreach ($listClasses as $numClass => $time) {
                $array = $classSchedules[$numClass] ?? collect();
                $rows .= self::addRow($titles, 
                        $array[self::FIRST_WEEK] ?? collect(), 
                        $funcAddSchedule, 
                        ['id' => $numClass, 'time' => $time], 
                        $day != $currentDay ? $day : 0
                    ) 
                    . self::addRow($titles, $array[self::SECOND_WEEK] ?? collect(), $funcAddSchedule);
                $currentDay = $day;
            }
            
        }
        return str_replace('{rows}', $rows, $html);
    }

    protected static function addRow(array $titles, Collection $schedules, callable $condition, array $class = [], int $day = 0) : string
    {        
        $rowTemplate = '<tr>{day}{class}{schedules}</tr>';
        $dayTemplate = $day > 0 ? '<td rowspan="14" id="day">{nameDay}</td>' : '';
        $classTemplate = !empty($class) ? '<td rowspan="2" id="class">{numClass}</td><td rowspan="2" id="time">{timeClass}</td>' : '';
        $schedulesHtml = self::createSubrowSchedule($schedules, $titles, $condition);
        $html = str_replace(
            ['{day}', '{class}', '{schedules}'], 
            [$dayTemplate, $classTemplate, $schedulesHtml], 
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