<?php

namespace App\ReadExcel;

use App\Models\Group;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Services\DisciplineService;
use App\Services\GroupScheduleService;
use App\Services\ScheduleService;
use App\Services\TeacherScheduleService;
use App\Services\TypeDisciplineService;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelParser extends TemplateScheduleParser
{
    /**
     * Регулярное выражения для поиска групп
     * @var string
     */
    const SEARCH_GROUP_PATTERN = '/\w+\d+\-\d+\-\d+/ui';

    /**
     * Столбец с днями недели
     */
    const DAY_COLUMN_INDEX = 'A';

    /**
     * Столбец с номером пары
     */
    const CLASS_NUMBER_COLUMN_INDEX = 'B';

    /**
     * Столбец с номером аудитории
     */
    const CLASSROOM_COLUMN_INDEX = 'I';

    /**
     * Столбцы с дисциплинами
     */
    const DISCIPLINE_COLUMNS_INDEXES = ['F', 'K'];

    protected $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function processSheet(Worksheet $sheet)
    {
        $this->sheet = $sheet;
        $highestRow = $this->sheet->getHighestRow();
        for ($row = 4; $row <= $highestRow; $row++) {
            foreach (static::DISCIPLINE_COLUMNS_INDEXES as $col) {
                $this->processSchedule($row, $col);
            }
        }
    }

    protected function processSchedule(&$row, string $col)
    {
        $discipline = $this->getCellValue($row, $col);
        if (!$discipline) {
            return;
        }        
        $schedule = $this->addSchedule($row, $col, $discipline);
        if (!$schedule){
            return;
        }
        // связь расписания с преподами 
        $teacherCol = $this->getCellValue($row, static::nextLetter($col, 2));
        $this->addTeacherSchedules($teacherCol, $schedule);
        
        // связь расписания с группами
        $group = Group::where('nameGroup', $this->getCellValue(static::GROUP_ROW, $col));
        if ($group->first()) {
            GroupScheduleService::addGroupSchedule($group->first(), $schedule);
        } else {
            // @todo нет группы в расписании, т.к. ее нет в базе
            // выгрузить в справочник ошибок
        }
    }

    protected function addTeacherSchedules(string $teacherCol, Schedule $schedule)
    {
        $teacherList = explode("\n", $teacherCol);        
        foreach ($teacherList as $nameTeacher) {            
            $teacher = Teacher::where('shortNameTeacher', '=', $nameTeacher)->first();            
            if ($teacher) {
                TeacherScheduleService::addTeacherSchedule($teacher, $schedule);
            } else {
                // @todo справочник ошибок для админа!!! 
                // есть препод в расписании, но нет в базе -> в расписании нет препода
            }
        }
    }

    protected function addSchedule($row, string $col, string $discipline)
    {    
        $shortNameTypeDisc = static::skipRepeats($this->getCellValue($row, static::nextLetter($col, 1)));     
        if (!$shortNameTypeDisc){
            return false;    
        }
        $classroom = static::getClassroom($this->getCellValue($row, static::nextLetter($col, 3)));

        $attributes = [
            'day' => static::WEEK[$this->getCellValue($row - (($row - 4) % 14), static::DAY_COLUMN_INDEX)] ?? static::NOT_DAY,
            'week' => $row % 2 == 0 ? static::FIRST_WEEK : static::SECOND_WEEK,
            'class' => $this->getCellValue($row % 2 == 0 ? $row : $row - 1, static::CLASS_NUMBER_COLUMN_INDEX),
            'discipline_id' => DisciplineService::addDiscipline(static::skipRepeats($discipline))->value('id'),
            'classroom_id' => $classroom ? $classroom['id'] : $classroom,
            'type_discipline_id' => TypeDisciplineService::addTypeDiscipline($shortNameTypeDisc)->value('id'),
        ];

        $schedule = ScheduleService::addSchedule($attributes);
      
        return $schedule;
    }

}
