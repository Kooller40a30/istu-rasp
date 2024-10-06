<?php

namespace App\ReadExcel;

use App\Models\Classroom;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Абстрактный парсер расписания на основе шаблона Excel.
 */
abstract class TemplateScheduleParser 
{
    /**
     * Номер строки, содержащей группы.
     *
     * @var int
     */
    const INDEX_GROUPS_ROW = 2;

    /**
     * Номер первой недели (верхняя строка).
     */
    const FIRST_WEEK_NUMBER = 1;

    /**
     * Номер второй недели (нижняя строка).
     */
    const SECOND_WEEK_NUMBER = 2;

    /**
     * Регулярное выражение для поиска номера аудитории.
     *
     * @var string
     */
    const REGEX_CLASSROOM_PATTERN = '/\d+\-\d+\w*/ui';

    /**
     * Ассоциативный массив дней недели с их номерами.
     *
     * @var array
     */
    const DAYS_OF_WEEK = [
        'ПОНЕДЕЛЬНИК' => 1,
        'ВТОРНИК'     => 2,
        'СРЕДА'       => 3,
        'ЧЕТВЕРГ'     => 4,
        'ПЯТНИЦА'     => 5,
        'СУББОТА'     => 6,
        'пн'          => 1,
        'вт'          => 2,
        'ср'          => 3,
        'чт'          => 4,
        'пт'          => 5,
        'сб'          => 6,
        'вс'          => 7,
    ];

    /**
     * Значение, указывающее, что строка не соответствует дню недели.
     */
    const INVALID_DAY = -1;

    /**
     * Лист Excel, содержащий расписание.
     *
     * @var Worksheet
     */
    protected $worksheet;

    /**
     * Обрабатывает лист Excel и извлекает расписание.
     *
     * @param Worksheet $worksheet Лист Excel для обработки.
     * @return void
     */
    abstract public function processSheet(Worksheet $worksheet);

    /**
     * Обрабатывает отдельную строку расписания.
     *
     * @param mixed  &$row    Данные строки для обработки.
     * @param string $column  Буква столбца.
     * @return void
     */
    abstract protected function processSchedule(&$row, string $column);    

    /**
     * Генерирует следующую букву в алфавите с учётом повторений.
     *
     * @param string $base    Базовая буква.
     * @param int    $repeat  Количество повторений.
     * @return string         Следующая буква.
     */
    protected static function getNextLetter(string $base, int $repeat = 0): string
    {
        if ($repeat <= 0) {
            return $base;
        }
        return static::getNextLetter(++$base, --$repeat);
    }

    /**
     * Удаляет повторяющиеся строки из текста.
     *
     * @param string $text Исходный текст с разделителями строк.
     * @param bool   $all  Если true, возвращает все уникальные строки, иначе только первую.
     * @return array|string Массив уникальных строк или первая уникальная строка.
     */
    protected static function removeDuplicateLines(string $text, bool $all = false)
    {
        $uniqueLines = array_unique(explode("\n", $text));        
        return $all ? $uniqueLines : ($uniqueLines[0] ?? $text);
    }
    
    /**
     * Получает значение ячейки на листе Excel.
     *
     * @param int|string $row    Номер строки.
     * @param string     $column Буква столбца.
     * @return mixed            Значение ячейки.
     */
    protected function getCellValue($row, $column)
    {
        return $this->worksheet->getCell($column . $row)->getValue();
    }

    /**
     * Находит аудиторию по номеру.
     *
     * @param string|null $room Номер аудитории.
     * @return Classroom|null  Найденная аудитория или null, если не найдена.
     */
    protected static function findClassroom(?string $room): ?Classroom
    {
        if (empty($room)) {
            return null;
        }

        preg_match(static::REGEX_CLASSROOM_PATTERN, $room, $matches);
        $classroomNumber = $matches[0] ?? null;

        if ($classroomNumber) {
            return Classroom::where('numberClassroom', $classroomNumber)->first();
        }

        return null;
    }
}
