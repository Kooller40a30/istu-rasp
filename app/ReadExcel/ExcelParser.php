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
use Illuminate\Support\Facades\Log;

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
        $this->worksheet = $sheet;
        $highestRow = $this->worksheet->getHighestRow();
        Log::info("Начало обработки файла {$this->fileName}. Всего строк: {$highestRow}");
        for ($row = 4; $row <= $highestRow; $row++) {
            Log::info("Обработка строки: {$row}");
            foreach (static::DISCIPLINE_COLUMNS_INDEXES as $col) {
                $this->processSchedule($row, $col);
            }
        }
        Log::info("Завершена обработка файла {$this->fileName}");
    }

    protected function processSchedule(&$row, string $col)
    {
        $discipline = $this->getCellValue($row, $col);
        if (!$discipline) {
            Log::debug("Пустая дисциплина в строке {$row}, колонка {$col}. Пропускаем.");
            return;
        }
        Log::info("Найдена дисциплина '{$discipline}' в строке {$row}, колонка {$col}");

        $schedule = $this->addSchedule($row, $col, $discipline);
        if (!$schedule) {
            Log::warning("Не удалось создать расписание для дисциплины '{$discipline}' в строке {$row}, колонка {$col}");
            return;
        }
        Log::info("Расписание создано для дисциплины '{$discipline}' в строке {$row}, колонка {$col}");

        // Связь расписания с преподавателями
        $teacherCol = $this->getCellValue($row, static::getNextLetter($col, 2));
        $this->addTeacherSchedules($teacherCol, $schedule);

        // Связь расписания с группами
        $groupCell = $this->getCellValue(static::INDEX_GROUPS_ROW, $col);
        $groupQuery = Group::where('nameGroup', $groupCell);
        if ($groupQuery->first()) {
            GroupScheduleService::addGroupSchedule($groupQuery->first(), $schedule);
            Log::info("Связано расписание с группой '{$groupCell}'");
        } else {
            Log::error("Группа '{$groupCell}' не найдена в базе. Строка {$row}, колонка {$col}");
            // @todo: добавить запись в справочник ошибок
        }
    }

    protected function addTeacherSchedules(string $teacherCol, Schedule $schedule)
    {
        $teacherList = explode("\n", $teacherCol);
        foreach ($teacherList as $nameTeacher) {
            $nameTeacher = trim($nameTeacher);
            if (empty($nameTeacher)) {
                continue;
            }
            $teacher = Teacher::where('shortNameTeacher', '=', $nameTeacher)->first();
            if ($teacher) {
                TeacherScheduleService::addTeacherSchedule($teacher, $schedule);
                Log::info("Преподаватель '{$nameTeacher}' связан с расписанием ID: {$schedule->id}");
            } else {
                Log::error("Преподаватель '{$nameTeacher}' не найден в базе данных для расписания ID: {$schedule->id}");
                // @todo: добавить запись ошибки для админа
            }
        }
    }

    protected function addSchedule($row, string $col, string $discipline)
    {
        $shortNameTypeDisc = static::removeDuplicateLines($this->getCellValue($row, static::getNextLetter($col, 1)));
        if (!$shortNameTypeDisc) {
            Log::warning("Отсутствует тип дисциплины в строке {$row}, колонка " . static::getNextLetter($col, 1));
            return false;
        }
        $classroom = static::findClassroom($this->getCellValue($row, static::getNextLetter($col, 3)));

        $dayCell = $this->getCellValue($row - (($row - 4) % 14), static::DAY_COLUMN_INDEX);
        $day = static::DAYS_OF_WEEK[$dayCell] ?? static::INVALID_DAY;
        $week = $row % 2 == 0 ? static::FIRST_WEEK_NUMBER : static::SECOND_WEEK_NUMBER;
        $class = $this->getCellValue($row % 2 == 0 ? $row : $row - 1, static::CLASS_NUMBER_COLUMN_INDEX);

        $disciplineId = DisciplineService::addDiscipline(static::removeDuplicateLines($discipline))->value('id');
        $typeDisciplineId = TypeDisciplineService::addTypeDiscipline($shortNameTypeDisc)->value('id');

        $attributes = [
            'day'               => $day,
            'week'              => $week,
            'class'             => $class,
            'discipline_id'     => $disciplineId,
            'classroom_id'      => $classroom ? $classroom['id'] : null,
            'type_discipline_id'=> $typeDisciplineId,
        ];

        Log::info("Создаем расписание с атрибутами: " . json_encode($attributes));

        $schedule = ScheduleService::addSchedule($attributes);

        if ($schedule) {
            Log::info("Расписание успешно создано. ID: {$schedule->id}");
        } else {
            Log::error("Не удалось создать расписание с атрибутами: " . json_encode($attributes));
        }

        return $schedule;
    }
}
