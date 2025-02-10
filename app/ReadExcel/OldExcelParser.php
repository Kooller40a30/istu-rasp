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
use App\Services\ErrorService;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Log;

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

        Log::info("Начало обработки файла {$this->fileName}. Всего строк: {$highestRow}");

        for ($row = 3; $row < $highestRow; $row += static::DELTA_SCHEDULE) {
            Log::info("Обработка строки: {$row}");
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
        Log::info("Определены столбцы: " . json_encode($this->columns));
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
        Log::info("Начало обработки расписания. Строка: {$row}, Колонка: {$col}");
        $schedule = $this->addSchedule($row, $col);
        if (!$schedule) {
            Log::warning("Расписание не найдено или не создано для строки {$row}, колонки {$col}");
            return;
        }

        // Проверка корректности данных преподавателя
        $shortNameTeacher = $this->getCellValue($row + 2, $col);
        $teacher = Teacher::where('shortNameTeacher', $shortNameTeacher)->first();
        if (!$teacher) {
            Log::error("Преподаватель не найден: {$shortNameTeacher} в файле {$this->fileName} на строке {$row}");
            ErrorService::teacherDataError($shortNameTeacher, [
                'file'   => $this->fileName,
                'day'    => $this->getDayAndWeekCell($row)[0],
                'week'   => $this->getDayAndWeekCell($row)[1],
                'group'  => $this->getCellValue(static::INDEX_GROUPS_ROW, $col),
                'class'  => $this->getCellValue($row, $this->nextLetter($col, 1)),
                'value'  => "Teacher not exist in database. Please, update entries"
            ]);
            return;
        }

        // Проверка корректности данных группы
        $groupName = $this->getCellValue(static::INDEX_GROUPS_ROW, $col);
        if (!$this->isValidGroupFormat($groupName)) {
            Log::error("Неверный формат группы: {$groupName} в файле {$this->fileName} на строке {$row}");
            ErrorService::groupDataError($groupName, [
                'file'   => $this->fileName,
                'day'    => $this->getDayAndWeekCell($row)[0],
                'week'   => $this->getDayAndWeekCell($row)[1],
                'class'  => $this->getCellValue($row, $this->nextLetter($col, 1)),
                'value'  => 'Invalid group data format'
            ]);
            return;
        } else {
            $group = Group::where('nameGroup', $groupName)->first();
            if (!$group) {
                Log::error("Группа не найдена в базе: {$groupName} в файле {$this->fileName} на строке {$row}");
                ErrorService::groupDataError($groupName, [
                    'file'   => $this->fileName,
                    'day'    => $this->getDayAndWeekCell($row)[0],
                    'week'   => $this->getDayAndWeekCell($row)[1],
                    'class'  => $this->getCellValue($row, $this->nextLetter($col, 1)),
                    'value'  => "Group not exist in database. Please, update entries"
                ]);
                return;
            }
        }

        // Привязываем расписание к группе и преподавателю
        GroupScheduleService::addGroupSchedule($group, $schedule);
        TeacherScheduleService::addTeacherSchedule($teacher, $schedule);
        Log::info("Расписание успешно добавлено для группы {$groupName} и преподавателя {$shortNameTeacher}.");
    }

    protected function addSchedule(&$row, string $col)
    {
        $dayAndWeek = $this->getDayAndWeekCell($row);
        list($day, $week) = $dayAndWeek;

        if ($day == static::WEEKEND) {
            Log::info("Пропуск расписания: выходной день (вс) на строке {$row}");
            $row++;
            return false;
        }

        $typeDisc = $this->getCellValue($row, $col);
        if (!$typeDisc) {
            Log::warning("Пустой тип дисциплины на строке {$row}, колонке {$col}");
            return false;
        }

        $disc = $this->getCellValue($row + 1, $col);
        $classroomData = static::findClassroom($this->getCellValue($row, static::nextLetter($col, 2)));
        $classroom_id = $classroomData['id'] ?? null;
        $time = date('H:i:s', strtotime(str_replace('-', ':', $this->getTimeCell($row, $col))));
        $class = ClassModel::where('start_time', $time)->first()['id'] ?? null;
        $discipline_id = DisciplineService::addDiscipline($disc)->value('id');
        $type_discipline_id = TypeDisciplineService::addTypeDiscipline($typeDisc)->value('id');

        Log::info("Добавление расписания: день {$day}, неделя {$week}, время {$time}, дисциплина {$disc}");

        return ScheduleService::addSchedule([
            'day'               => $day,
            'week'              => $week,
            'class'             => $class,
            'discipline_id'     => $discipline_id,
            'classroom_id'      => $classroom_id,
            'type_discipline_id'=> $type_discipline_id,
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
            Log::warning("Не удалось определить день недели на строке {$row}. Используем значения по умолчанию.");
            return [static::DAYS_OF_WEEK["вс"], static::FIRST_WEEK_NUMBER];
        }
        $day = static::DAYS_OF_WEEK[$matches[1]];
        $weekText = $matches[2] ?? 'над';
        $week = mb_stripos($weekText, 'над', 0) !== false ? static::FIRST_WEEK_NUMBER : static::SECOND_WEEK_NUMBER;

        Log::info("Определены день: {$day}, неделя: {$week} для строки {$row}");
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
