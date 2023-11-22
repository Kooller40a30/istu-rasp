<?php

namespace App\Helpers;

use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Services\GetFromDatabase\ScheduleRepository;

class ScheduleHelper 
{
    const FIRST_WEEK = 1;
    const SECOND_WEEK = 2;

    public static $days = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];

    public static function generateGroupSchedule(Group $group, bool $compress = false) : string
    {
        // 9 столбцов для n-го числа групп
        // 15 строк для 1 группы
        $firstSchedules = ScheduleRepository::findSchedules($group, self::FIRST_WEEK)->toArray();
        $secondSchedules = ScheduleRepository::findSchedules($group, self::SECOND_WEEK)->toArray();
        $html = self::createHeaderTable($group);
        // dd($firstSchedules);
        $classes = ClassModel::orderBy('id')->get();
        foreach ($classes as $class) {
            
            $firstSchedule = array_filter($firstSchedules, function(array $schedule) use ($class) {
                return $schedule['class'] === $class['id'];
            });
            $secondSchedule = array_filter($secondSchedules, function(array $schedule) use ($class) {
                return $schedule['class'] === $class['id'];
            });
            $htmlFirstSchedule = self::createSubrowSchedule($firstSchedule);
            $htmlSecondSchedule = self::createSubrowSchedule($secondSchedule);
            $numLesson = $class['id'];
            $timeLesson = $class['start_time'] . ' - ' . $class['end_time'];

            $html .= "<tr>
                <td rowspan=\"2\">$numLesson</td>
                <td rowspan=\"2\">$timeLesson</td>
                $htmlFirstSchedule
            </tr>
            <tr>$htmlSecondSchedule</tr>";
        }
        return $html;
    }

    protected static function createSubrowSchedule(array $schedules)
    {
        $days = range(1, 6);
        $listTd = [];
        foreach ($days as $day) {
            $listTd[$day] = '<td></td>';
        }
        foreach ($schedules as $schedule) {
            if (in_array($schedule['day'], $days)) {
                $disc = $schedule['content'];
                $listTd[$schedule['day']] = "<td>$disc</td>";  
            }          
        }
        return implode('', $listTd);
    }

    protected static function createHeaderTable(Group $group)
    {
        $nameGroup = $group['nameGroup'];
        $week  = range(1, 6);        
        $html = "<tr><td rowspan=\"15\" style=\"writing-mode: tb-rl;\">{$nameGroup}</td><td>Пара</td><td>Время</td>";
        foreach ($week as $day) {
            $html .= '<td>' . self::getDay($day) . '</td>';
        }
        return $html . '</tr>';
    }

    public static function generateCourseSchedule(Faculty $faculty, Course $course = null) : string
    {
        $html = "";
        $groupsModel = $faculty->groups();
        if ($course) {
            $groupsModel->where('course_id', '=', $course['id']);
        }
        $groups = $groupsModel->get();
        foreach ($groups as $group) {
            $html .= self::generateGroupSchedule($group, true);
        }
        return $html;
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

    public static function generateTeacherSchedule(Teacher $teacher) : string
    {
        $html = self::createHeaderTableTeacher($teacher);

        return $html;
    }

    public static function createHeaderTableTeacher(Teacher $teacher) 
    {
        $name = $teacher['nameTeacher'];
        $html = "<tr><td rowspan=\"2\">$name</td>";
        foreach (self::$days as $day) {
            $html .= "<td>$day</td>";
        }
        return $html . '</tr>';
    }

    public static function generateTeachersSchedule(Faculty $faculty, Department $department) : string
    {

    }
}