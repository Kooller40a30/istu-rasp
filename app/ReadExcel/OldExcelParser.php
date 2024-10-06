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
use App\Services\ErrorService;
use Carbon\Carbon;

class OldExcelParser extends TemplateScheduleParser
{
    /**
     * Переход к следующему расписанию
     */
    const DELTA_SCHEDULE = 3;

    /**
     * Воскресенье
     */
    const WEEKEND = 7;

    protected $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    protected $columns = [];

    public function processSheet(Worksheet $sheet)
    {
        $this->worksheet = $sheet;
        $this->defineColumns(); // Определяем столбцы динамически
        $highestRow = $this->worksheet->getHighestRow();

        for ($row = 3; $row < $highestRow; $row += static::DELTA_SCHEDULE) {
            foreach ($this->columns['group_columns'] as $colGroup) {
                $this->processSchedule($row, $colGroup);
            }
        }
    }

    /**
     * Определяем столбцы на основе структуры листа.
     */
    protected function defineColumns()
    {
        // Логика для определения динамических столбцов
        $this->columns['group_columns'] = $this->findGroupColumns();
        $this->columns['days_and_week_column'] = $this->findDaysAndWeekColumn();
    }

    protected function findGroupColumns()
    {
        $columns = [];
        $row = 2; // строка, в которой ожидается наличие групп

        for ($col = 'A'; $col <= 'Z'; $col++) {
            $value = $this->getCellValue($row, $col);
            if ($this->isValidGroupFormat($value)) {
                $columns[] = $col;
            }
        }

        return $columns;
    }

    /*
    protected function isValidTeacherFormat(string $shortName): bool
    {
        // Пример проверки: Фамилия И.О.
        return preg_match('/^[А-ЯЁ][а-яё]+\s[А-ЯЁ]\.[А-ЯЁ]\.$/u', $shortName) === 1;
    }
        неактуально, т.к. есть нестандартные фио
    */

    /**
     * Проверяет, соответствует ли строка корректному формату группы.
     *
     * @param string $value Название группы
     * @return bool
     */
    protected function isValidGroupFormat($value): bool
    {
        $value = trim($value);
        return preg_match('/^[А-ЯЁ]\d{2}-\d{3}-\d$/u', $value) === 1;
    }

    /**
     * Пример поиска столбца с днями и неделями.
     */
    protected function findDaysAndWeekColumn()
    {
        for ($col = 'A'; $col <= 'Z'; $col++) {
            $header = $this->getCellValue(2, $col);
            // сведения о дне недели и четности лежит в колонке под именем "число"
            if (mb_stripos($header, 'число') !== false) {
                return $col;
            }
        }
        return 'A'; // Значение по умолчанию
    }

    protected function processSchedule(&$row, string $col)
    {
        $schedule = $this->addSchedule($row, $col);
        if (!$schedule) {
            return;
        }
        // Проверка корректности данных преподавателя
        $shortNameTeacher = $this->getCellValue($row + 2, $col);
        $teacher = Teacher::where('shortNameTeacher', $shortNameTeacher)->first();
        if (!$teacher) {
            ErrorService::teacherDataError($shortNameTeacher, [
                'file' => $this->fileName,
                'day' => $this->getDayAndWeekCell($row)[0],
                'week' => $this->getDayAndWeekCell($row)[1],
                'group' => $this->getCellValue(static::INDEX_GROUPS_ROW, $col),
                'class' => $this->getCellValue($row, $this->nextLetter($col, 1)),
                'value' => "Teacher not exist in database. Please, update entries"
                // Другие необходимые поля
            ]);
            return;
        }

        // Проверка корректности данных группы
        $groupName = $this->getCellValue(static::INDEX_GROUPS_ROW, $col);
        if (!$this->isValidGroupFormat($groupName)) {
            ErrorService::groupDataError($groupName, [
                'file' => $this->fileName,
                'day' => $this->getDayAndWeekCell($row)[0],
                'week' => $this->getDayAndWeekCell($row)[1],
                'class' => $this->getCellValue($row, $this->nextLetter($col, 1)),
                'value' => 'Invalid group data format'
                // Другие необходимые поля
            ]);
            return;
        } else {
            $group = Group::where('nameGroup', $groupName)->first();
            if (!$group) {
                ErrorService::groupDataError($groupName, [
                    'file' => $this->fileName,
                    'day' => $this->getDayAndWeekCell($row)[0],
                    'week' => $this->getDayAndWeekCell($row)[1],
                    'class' => $this->getCellValue($row, $this->nextLetter($col, 1)),
                    'value' => "Group not exist in database. Please, update entries"
                ]);
                return;
            }
        }

        GroupScheduleService::addGroupSchedule($group, $schedule);
        TeacherScheduleService::addTeacherSchedule($teacher, $schedule);

        /*// Проверка корректности данных преподавателя
        $class = $this->getCellValue($row + 2, $col);
        $teacher = Teacher::where('shortNameTeacher', $shortNameTeacher)->first();
        if ($teacher) {
            TeacherScheduleService::addTeacherSchedule($teacher, $schedule);
        } else {
            ErrorService::teacherDataError($shortNameTeacher, [
                'file' => $this->fileName,
                'day' => $this->getDayAndWeekCell($row)[0],
                'week' => $this->getDayAndWeekCell($row)[1],
                'group' => $this->getCellValue(static::GROUP_ROW, $col),
                'value' => 'Teacher not exist in database. Please, update entries'
                // Другие необходимые поля
            ]);
            return;
        }*/
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
        $classroom_id = static::findClassroom($this->getCellValue($row, static::nextLetter($col, 2)))['id'] ?? null;
        $time = date('H:i:s', strtotime(str_replace('-', ':', $this->getTimeCell($row, $col))));
        $class = ClassModel::where('start_time', $time)->first()['id'] ?? null;
        $discipline_id = DisciplineService::addDiscipline($disc)->value('id');
        $type_discipline_id = TypeDisciplineService::addTypeDiscipline($typeDisc)->value('id');

        return ScheduleService::addSchedule([
            'day' => $day,
            'week' => $week,
            'class' => $class,
            'discipline_id' => $discipline_id,
            'classroom_id' => $classroom_id,
            'type_discipline_id' => $type_discipline_id,
        ]);
    }

    /**
     * Получает день и неделю из ячейки.
     *
     * @param int $row Номер строки
     * @return array Массив с днем и неделей
     */
    protected function getDayAndWeekCell($row)
    {
        $text = $this->getCellValue($row, $this->columns['days_and_week_column']);
        if (!$text) {
            return $this->getDayAndWeekCell($row - 1);
        }

        $dayAndWeekText = mb_strtolower($text, 'UTF-8');

        $matches = [];
        preg_match('/(\w+)\s+\(?(\w+)\)?/ui', $dayAndWeekText, $matches);
        if (!isset($matches[1])) {
            return [static::DAYS_OF_WEEK["вс"], static::FIRST_WEEK_NUMBER];
        }
        $day = static::DAYS_OF_WEEK[$matches[1]];
        $weekText = $matches[2] ?? 'над';
        $week = mb_stripos($weekText, 'над', 0) !== false ? static::FIRST_WEEK_NUMBER : static::SECOND_WEEK_NUMBER;

        return [$day, $week];
    }

    /**
     * Получает время из ячейки.
     *
     * @param int $row Номер строки
     * @param string $col Буква столбца
     * @return string Время в формате H:i:s
     */
    protected function getTimeCell($row, $col)
    {
        $time = $this->getCellValue($row, static::nextLetter($col, 1));
        if ($time || $row < 1) {
            return $time;
        }
        return $this->getTimeCell($row - 1, $col);
    }

    /**
     * Возвращает следующую букву столбца с заданным смещением.
     *
     * @param string $col Текущая буква столбца
     * @param int $offset Смещение
     * @return string Следующая буква столбца
     */
    protected static function nextLetter($col, $offset = 1)
    {
        $num = ord($col) - 64; // 'A' => 1
        $num += $offset;
        return chr($num + 64);
    }
}
