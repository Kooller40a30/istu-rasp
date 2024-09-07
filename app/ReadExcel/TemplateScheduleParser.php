<?php

namespace App\ReadExcel;

use App\Models\Classroom;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class TemplateScheduleParser 
{
    /**
     * Строка с группами
     * @var integer
     */
    const GROUP_ROW = 2;

    /**
     * Неделя над чертой
     */
    const FIRST_WEEK = 1;

    /**
     * Неделя под чертой
     */
    const SECOND_WEEK = 2;

    /**
     * Регулярное выражение для поиска аудитории
     * @var string
     */
    const SEARCH_CLASSROOM_PATTERN = '/\d+\-\d+\w*/ui';

    /**
     * Неделя
     */
    const WEEK = [
        'ПОНЕДЕЛЬНИК' => 1,
        'ВТОРНИК' => 2,
        'СРЕДА' => 3,
        'ЧЕТВЕРГ' => 4,
        'ПЯТНИЦА' => 5,
        'СУББОТА' => 6,
        'пн' => 1,
        'вт' => 2,
        'ср' => 3,
        'чт' => 4,
        'пт' => 5,
        'сб' => 6,
        'вс' => 7,
    ];

    /**
     * Не день недели
     */
    const NOT_DAY = -1;

    /**
     * Лист Excel
     *
     * @var Worksheet
     */
    protected $sheet;

    abstract public function processSheet(Worksheet $sheet);

    abstract protected function processSchedule(&$row, string $col);    

    protected static function nextLetter(string $base, int $repeat = 0)
    {
        if ($repeat <= 0) {
            return $base;
        }
        return static::nextLetter(++$base, --$repeat);
    }

    protected static function skipRepeats($text, $all = false)
    {
        $array = array_unique(explode("\n", $text));        
        return $all ? $array : ($array[0] ?? $text);
    }

    protected function getCellValue($row, $col)
    {
        return $this->sheet->getCell($col . $row)->getValue();
    }

    protected static function getClassroom(string $room = null)
    {
        $matches = [];
        preg_match(static::SEARCH_CLASSROOM_PATTERN, $room, $matches);
        $numberClassroom = $matches[0] ?? null;
        return Classroom::where('numberClassroom', $numberClassroom)->first();
    }
}