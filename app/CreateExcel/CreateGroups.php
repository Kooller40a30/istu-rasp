<?php

namespace App\CreateExcel;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

abstract class CreateGroups
{
    public static function createGroups($course,$faculty_id){

        $groups = Group::where('course',$course)->where('faculty_id',$faculty_id)->get();
        $faculty = Faculty::where('id',$faculty_id)->value('shortNameFaculty');
        $name = $faculty . ' ' . $course;
        return CreateGroups::createScheduleGroups($groups, $name);
    }

    public static function createGroup($group_id){

        $group = Group::where('id',$group_id)->get();
        $schedules = $group[0]->schedules;
        $nameGroup = $group[0]['nameGroup'];
        return CreateGroups::createScheduleGroup($schedules, $nameGroup);
    }

    public static function createScheduleGroup($schedules, $nameGroup){

        $filePattern = 'app/public/patterns/pattern2.xlsx';
        $storage_path = storage_path($filePattern);
        $reader = IOFactory::createReaderForFile($storage_path);
        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($storage_path);

        //Получаем текущий активный лист
        $sheet = $spreadsheet->getActiveSheet();
        $col = 'A';
        $sheet->setCellValue('A1', $nameGroup);

        foreach ($schedules as $schedule) {
            //данные
            $day = $schedule['day'];
            $week = $schedule['week'];
            $class = $schedule['class'];

            //значение ячейки
            $value = $schedule['content'];

            //получаем нужную строку
            $row = $class*2 + $week - 1;

            //получаем нужный столбец
            $colNumber = 2 + $day;
            switch ($colNumber) {
                case 3: $col = 'C'; break;
                case 4: $col = 'D'; break;
                case 5: $col = 'E'; break;
                case 6: $col = 'F'; break;
                case 7: $col = 'G'; break;
                case 8: $col = 'H'; break;
            }

            $cell = $col . $row;

            $sheet->getColumnDimension($col)->setWidth(30);
            $sheet->getRowDimension($row)->setRowHeight(35);

            //записать полученное значение в ячейку
            $sheet->setCellValue($cell, $value);
        }

        $writer = new Xlsx($spreadsheet);
        //$nameGroup = str_replace('/','.',$nameGroup);
        $filename = ('//Расписание группы ' . $nameGroup);
        //$filePath = 'app\public\schedule' . $filename. '.xlsx';
        //$path = storage_path($filePath);
        //$writer->save($path);

        $filePath = 'app//public//schedule' . $filename . '.php';
        $path2 = storage_path($filePath);
        $writer2 = IOFactory::createWriter($spreadsheet, 'Html');
        $writer2->save($path2);

/*        $paths[0] = $path;
        $paths[1] = $path2;

        return $paths;*/
        return $path2;
    }
    public static function createScheduleGroups($groups, $name){

        $filePattern = 'app/public/patterns/pattern.xlsx';
        $storage_path = storage_path($filePattern);
        $reader = IOFactory::createReaderForFile($storage_path);
        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($storage_path);

        //Получаем текущий активный лист
        $sheet = $spreadsheet->getActiveSheet();

        CreateGroups::createSheetGroups($sheet,$groups);

        $writer = new Xlsx($spreadsheet);
        $filename = ('//Расписание групп ' . $name);

        //$filePath = 'app\public\schedule' . $filename . '.xlsx';
        //$path = storage_path($filePath);
        //$writer->save($path);

        $filePath = 'app//public//schedule' . $filename . '.php';
        $path2 = storage_path($filePath);
        $writer2 = IOFactory::createWriter($spreadsheet, 'Html');
        $writer2->save($path2);

/*        $paths[0] = $path;
        $paths[1] = $path2;

        return $paths;*/
        return $path2;
    }

    public static function createSheetGroups(&$sheet, $groups){
        $col = 'D';
        foreach ($groups as $group){
            //получаем расписание
            $schedules = $group->schedules;
            if (isset($schedules[0])) {
                //записываем преподавателя или аудиторию
                $sheet->setCellValue($col . '1', $group['nameGroup']);
                foreach ($schedules as $schedule) {
                    //данные
                    $day = $schedule['day'];
                    $week = $schedule['week'];
                    $class = $schedule['class'];
                    //значение ячейки
                    $value = $schedule['content'];

                    //получаем нужную строку
                    $row = ($day - 1) * 14 + 2 * $class + $week - 1;

                    $cell = $col . $row;

                    $sheet->getColumnDimension($col)->setWidth(30);
                    $sheet->getRowDimension($row)->setRowHeight(35);
                    //записать значение в ячейку
                    $sheet->setCellValue($cell, $value);
                }
                $col++;
            }
        }
        $highestColumn = $sheet->getHighestColumn();
        for ($c = $col;$c<=$highestColumn;$c++){
            $sheet->removeColumn($col);
        }
    }
}