<?php 

namespace App\Services;

use App\ReadExcel\GetPath;
use App\ReadExcel\ReadExcelClassroom;
use App\ReadExcel\ReadExcelDepartment;
use App\ReadExcel\ReadExcelFaculty;
use App\ReadExcel\ReadExcelGroup;
use App\ReadExcel\ReadExcelTeacher;
use App\ReadExcel\TemplateExcelReader;
use Exception;

class FileService
{
    const TYPE_FILE_CLASSROOM = 0;
    const TYPE_FILE_GROUP = 1;
    const TYPE_FILE_DEP = 2;
    const TYPE_FILE_TEACHER = 3;
    const TYPE_FILE_SCHEDULE = 4;
    const TYPE_FILE_FACULTY = 5;

    public static function getTypeFiles()
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

    public static function processFiles(int $type, array $files)
    {
        $types = static::getTypeFiles();
        
        if (!key_exists($type, $types)) {
            throw new Exception('Передан неопределенный тип файла. Обработка невозможна!');
        }

        //@todo избавиться от костыля с if
        if ($type == static::TYPE_FILE_SCHEDULE) {
            // обработка расписания
            $excelReader = new TemplateExcelReader();
            $excelReader->processFiles($files);
        } else {
            foreach ($files as $file) {
                $path = GetPath::savePath($file);
                if ($type == static::TYPE_FILE_GROUP) {
                    // обработка справочника групп
                    ReadExcelGroup::readFile($path);
                } elseif ($type == static::TYPE_FILE_TEACHER) {
                    ReadExcelTeacher::readFile($path);
                } elseif ($type == static::TYPE_FILE_CLASSROOM) {
                    // обработка справочника аудиторий
                    ReadExcelClassroom::readFile($path);
                } elseif ($type == static::TYPE_FILE_FACULTY) {
                    // обработка справочника факультетов/институтов
                    ReadExcelFaculty::readFile($path);
                } elseif ($type == static::TYPE_FILE_DEP) {
                    // обработка справочника кафедр
                    ReadExcelDepartment::readFile($path);
                }
            }
        }
    }
}