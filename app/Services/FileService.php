<?php 

namespace App\Services;

use App\ReadExcel\GetPath;
use App\ReadExcel\ReadExcelClassroom;
use App\ReadExcel\ReadExcelDepartment;
use App\ReadExcel\ReadExcelFaculty;
use App\ReadExcel\ReadExcelGroup;
use App\ReadExcel\ReadExcelTeacher;
use App\ReadExcel\TemplateExcelReader;
use InvalidArgumentException;

class FileService
{
    const TYPE_FILE_CLASSROOM = 0;
    const TYPE_FILE_GROUP = 1;
    const TYPE_FILE_DEP = 2;
    const TYPE_FILE_TEACHER = 3;
    const TYPE_FILE_SCHEDULE = 4;
    const TYPE_FILE_FACULTY = 5;

    // Карта обработчиков для простых типов файлов (кроме расписания)
    private const SIMPLE_READER_MAP = [
        self::TYPE_FILE_CLASSROOM => ReadExcelClassroom::class,
        self::TYPE_FILE_GROUP => ReadExcelGroup::class,
        self::TYPE_FILE_DEP => ReadExcelDepartment::class,
        self::TYPE_FILE_TEACHER => ReadExcelTeacher::class,
        self::TYPE_FILE_FACULTY => ReadExcelFaculty::class,
    ];

    public static function getTypeFiles(): array
    {
        return [
            static::TYPE_FILE_CLASSROOM => 'Аудитории', 
            static::TYPE_FILE_GROUP => 'Группы', 
            static::TYPE_FILE_DEP => 'Кафедры',
            static::TYPE_FILE_TEACHER => 'Преподаватели', 
            static::TYPE_FILE_SCHEDULE => 'Расписание',
            static::TYPE_FILE_FACULTY => 'Факультеты', 
        ];
    }

    public static function processFiles(int $type, array $files): void
    {
        $types = static::getTypeFiles();
        
        if (!key_exists($type, $types)) {
            throw new InvalidArgumentException('Передан неопределенный тип файла. Обработка невозможна!');
        }

        if ($type === static::TYPE_FILE_SCHEDULE) {
            // Обработка расписания (особый случай)
            $excelReader = new TemplateExcelReader();
            $excelReader->processFiles($files);
        } elseif (isset(self::SIMPLE_READER_MAP[$type])) {
            // Обработка других типов файлов через карту
            $readerClass = self::SIMPLE_READER_MAP[$type];
            foreach ($files as $file) {
                $path = GetPath::savePath($file);
                // Предполагаем, что у всех классов есть статический метод readFile
                $readerClass::readFile($path); 
            }
        } else {
             // Этот блок не должен выполниться из-за проверки key_exists,
             // но полезен для отладки, если карта или типы изменятся.
             throw new \LogicException("Необработанный тип файла в логике FileService: {$type}");
        }
    }
}