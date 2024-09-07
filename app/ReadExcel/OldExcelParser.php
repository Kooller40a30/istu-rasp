<?php

namespace App\ReadExcel;

use App\Models\ClassModel;
use App\Models\Group;
use App\Models\Teacher;
use App\Services\DisciplineService;
use App\Services\GroupScheduleService;
use App\Services\ScheduleService;
use App\Services\TeacherScheduleService;
use App\Services\TypeDisciplineService;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpParser\Node\Stmt\Static_;

use function PHPUnit\Framework\matches;

class OldExcelParser extends TemplateScheduleParser
{
    /**
     * Столбцы с группами (дисциплинами)
     */
    const GROUP_COLUMNS = ['B', 'F', 'J'];

    /**
     * Столбец с днями и тип недели (над чертой, под чертой)
     */
    const DAYS_AND_WEEK_COLUMN = 'A';

    /**
     * Переход к следующему расписанию
     */
    const DELTA_SCHEDULE = 3;

    /**
     * Воскресенье
     */
    const WEEKEND = 7;

    public function processSheet(Worksheet $sheet)
    {
        $this->sheet = $sheet;
        $highestRow = $this->sheet->getHighestRow();
        for ($row = 3; $row < $highestRow; $row+=static::DELTA_SCHEDULE) {
            foreach (static::GROUP_COLUMNS as $colGroup) {
                $this->processSchedule($row, $colGroup);
            }
        }        
    }

    protected function processSchedule(&$row, string $col)
    {
        $schedule = $this->addSchedule($row, $col);
        if (!$schedule) {
            return;
        }        
        
        $shortNameTeacher = $this->getCellValue($row + 2, $col);
        $teacher = Teacher::where('shortNameTeacher', $shortNameTeacher)->first();
        if ($teacher) {
            TeacherScheduleService::addTeacherSchedule($teacher, $schedule);
        } else {
            // @todo ошибки
        }

        $group = Group::where('nameGroup', $this->getCellValue(static::GROUP_ROW, $col))->first();
        if ($group) {
            GroupScheduleService::addGroupSchedule($group, $schedule);
        } else {
            // @todo нет группы в расписании, т.к. ее нет в базе
            // выгрузить в справочник ошибок
        }
    }

    protected function addSchedule(&$row, string $col)
    {
        $dayAndWeek = $this->getDayAndWeekCell($row);
        list($day, $week) = $dayAndWeek;
        if ($day == static::WEEKEND) {
            $row++;
            return false;
        }
        $typeDisc = $this->getCellValue($row, $col);
        if (!$typeDisc) {
            return false;
        }
        
        $disc = $this->getCellValue($row + 1, $col);
        $classroom_id = static::getClassroom($this->getCellValue($row, static::nextLetter($col, 2)))['id'] ?? null;
        $time = date('H:i:s', strtotime(str_replace('-', ':', $this->getTimeCell($row, $col))));
        $class = ClassModel::where('start_time', $time)->first()['id'] ?? null;
        $discipline_id = DisciplineService::addDiscipline($disc)->value('id');
        $type_discipline_id = TypeDisciplineService::addTypeDiscipline($typeDisc)->value('id');

        $schedule = ScheduleService::addSchedule([
            'day' => $day,
            'week' => $week,
            'class' => $class,
            'discipline_id' => $discipline_id,
            'classroom_id' => $classroom_id,                    
            'type_discipline_id' => $type_discipline_id,    
        ]);

        return $schedule;
    }

    protected function getDayAndWeekCell($row)
    {
        $text = $this->getCellValue($row, static::DAYS_AND_WEEK_COLUMN);
        if (!$text) {
            return $this->getDayAndWeekCell($row - 1);
        }
        
        $dayAndWeekText = mb_strtolower($text, 'UTF-8');
        // if (count($array) == 1) {
        //     $week = static::WEEK[mb_substr(trim($array[0]), 0, 2)];
        //     return [$week, static::FIRST_WEEK];
        // }
        $matches = [];
        preg_match('/(\w+)\s\(?(\w+)\)?/ui', $dayAndWeekText, $matches);
        if (!isset($matches[1])){
            return [static::WEEK["вс"], static::FIRST_WEEK];
        }
        $day = static::WEEK[$matches[1]];
        $weekText = $matches[2] ?? 'над';
        $week = mb_stripos($weekText, 'над', 0) !== false ? static::FIRST_WEEK : static::SECOND_WEEK;

        return [$day, $week];
    }

    protected function getTimeCell($row, $col)
    {
        $time = $this->getCellValue($row, static::nextLetter($col, 1));
        if ($time || $row < 1) {
            return $time;
        }
        return $this->getTimeCell($row - 1, $col);
    }
}