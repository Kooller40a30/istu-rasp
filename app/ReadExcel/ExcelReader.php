<?php

namespace App\ReadExcel;

use App\Models\Classroom;
use App\Models\Discipline;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\TypeDiscipline;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelReader 
{
    /**
     * Строка с группами
     * @var integer
     */
    const GROUP_ROW = 2;

    const FIRST_WEEK = 1;

    const SECOND_WEEK = 2;

    /**
     * Регулярное выражения для поиска групп
     * @var string
     */
    const SEARCH_GROUP_PATTERN = '/\w+\d+\-\d+\-\d+/ui';

    const SEARCH_CLASSROOM_PATTERN = '/\d+\-\d+\w*/ui';

    const DAY_COLUMN_INDEX = 'A';
    const CLASS_NUMBER_COLUMN_INDEX = 'B';
    const CLASSROOM_COLUMN_INDEX = 'I';
    const DISCIPLINE_COLUMNS_INDEXES = ['F', 'K'];

    const WEEK = [
        'ПОНЕДЕЛЬНИК' => 1,
        'ВТОРНИК' => 2,
        'СРЕДА' => 3,
        'ЧЕТВЕРГ' => 4,
        'ПЯТНИЦА' => 5,
        'СУББОТА' => 6,
    ];

    const NOT_DAY = -1;
    /**
     * Объект для чтения файла Excel
     *
     * @var IReader
     */
    private $reader;

    /**
     * Путь до Excel файла
     *
     * @var string
     */
    private $file;

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->reader = IOFactory::createReaderForFile($file);
    }

    public function processFile()
    {
        $spreadsheet = $this->reader->load($this->file);
        $sheets = $spreadsheet->getAllSheets();
        foreach ($sheets as $sheet) {
            $this->processSheet($sheet);
        }
    }

    protected function processSheet(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        // $highestColumn = $sheet->getHighestColumn();
        if ($sheet->getCell('B2')->getValue() != 'Группа') {
            // @todo второй парсер
            return;
        }
        for ($row = 4; $row <= $highestRow; $row++) {
            foreach (static::DISCIPLINE_COLUMNS_INDEXES as $col) {
                $this->processSchedule($sheet, $row, $col);
            }
        }
    }

    protected function processSchedule(Worksheet $sheet, $row, string $col)
    {
        $discipline = $sheet->getCell($col . $row)->getValue();
        if (!$discipline) {
            return;
        }        
        $schedule = $this->addSchedule($sheet, $row, $col, $discipline);
        // связь расписания с преподами 
        $teacherCol = $sheet->getCell(static::nextLetter($col, 2) . $row)->getValue();
        $this->addTeacherSchedules($teacherCol, $schedule);
        
        // связь расписания с группами
        $group = Group::where('nameGroup', $sheet->getCell($col . static::GROUP_ROW)->getValue());
        if ($group) {
            $this->addGroupSchedule($group, $schedule);
        } else {
            // @todo нет группы в расписании, т.к. ее нет в базе
            // выгрузить в справочник ошибок
        }
    }

    protected static function skipRepeats($text, $all = false)
    {
        $array = array_unique(explode("\n", $text));        
        return $all ? $array : ($array[0] ?? $text);
    }

    protected function addGroupSchedule(Builder $group, Schedule $schedule)
    {
        return GroupSchedule::firstOrCreate([
            'group_id' => $group->value('id'),
            'schedule_id' => $schedule['id'],
        ]);
    }

    protected function addTeacherSchedules(string $teacherCol, Schedule $schedule)
    {
        $teacherList = explode("\n", $teacherCol);        
        foreach ($teacherList as $nameTeacher) {            
            $teacher = Teacher::where('shortNameTeacher', '=', $nameTeacher)->first();            
            if ($teacher) {
                $this->addTeacherSchedule($teacher, $schedule);
            } else {
                // @todo справочник ошибок для админа!!! 
                // есть препод в расписании, но нет в базе -> в расписании нет препода
            }
        }
    }

    protected function addTeacherSchedule(Teacher $teacher, Schedule $schedule)
    {
        return TeacherSchedule::firstOrCreate([
            'teacher_id' => $teacher['id'],
            'schedule_id' => $schedule['id'],
        ]);
    }

    protected function addSchedule(Worksheet $sheet, $row, string $col, string $discipline)
    {    
        $discipline = static::skipRepeats($discipline);
        $shortNameTypeDisc = static::skipRepeats($sheet->getCell(static::nextLetter($col, 1) . $row)->getValue());
        
        $room = $sheet->getCell(static::nextLetter($col, 3) . $row)->getValue();
        $matches = [];
        preg_match(static::SEARCH_CLASSROOM_PATTERN, $room, $matches);
        $numberClassroom = $matches[0] ?? null;
        
        $classroom = Classroom::where('numberClassroom', $numberClassroom)->first();
        Discipline::firstOrCreate(['nameDiscipline' => $discipline]);
        TypeDiscipline::firstOrCreate(
            ['shortName' => $shortNameTypeDisc],
            ['name' => static::getTypeDisciplineName($shortNameTypeDisc)
        ]);

        $schedule = Schedule::firstOrCreate([
            'day' => static::WEEK[$sheet->getCell(static::DAY_COLUMN_INDEX . $row - (($row - 4) % 14))->getValue()] ?? static::NOT_DAY,
            'week' => $row % 2 == 0 ? static::FIRST_WEEK : static::SECOND_WEEK,
            'class' => $sheet->getCell(static::CLASS_NUMBER_COLUMN_INDEX . ($row % 2 == 0 ? $row : $row - 1))->getValue(),
            'discipline_id' => Discipline::where('nameDiscipline', $discipline)->value('id'),            
            'classroom_id' => $classroom ? $classroom['id'] : $classroom,
        ], [
            'type_discipline_id' => TypeDiscipline::where('shortName', $shortNameTypeDisc)->value('id'),
        ]);
      
        return $schedule;
    }

    protected static function nextLetter(string $base, int $repeat = 0)
    {
        if ($repeat <= 0) {
            return $base;
        }
        return static::nextLetter(++$base, --$repeat);
    }

    protected static function getTypeDisciplineName($shortName)
    {
        $names = [
            'Л' => 'Лекция',
            'П' => 'Практика',
            'ЛБ' => 'Лабораторная работа',
        ];
        return key_exists($shortName, $names) ? $names[$shortName] : null;
    }

}