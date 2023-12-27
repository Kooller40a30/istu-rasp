<?php

namespace App\ReadExcel;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ReadExcelClassroom
{
    public static function readFile($file)
    {
        Classroom::where('id', '>', 0)->delete();
        $reader = IOFactory::createReaderForFile($file);
        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($file);

        $sheetCount = $spreadsheet->getSheetCount();

        for ($i = 0; $i < $sheetCount; $i++) {
            //взять лист
            $sheet = $spreadsheet->getSheet($i);

            //пройтись по строкам
            $highestRow = $sheet->getHighestRow();
            for ($row = 2; $row <= $highestRow; $row++) {
                $numberClassroom = $sheet->getCell("F" . $row)->getValue();
                if ($numberClassroom != null) {
                    $building = $sheet->getCell("D" . $row)->getValue();
                    $faculty = $sheet->getCell("A" . $row)->getValue();
                    $numberDepartment = $sheet->getCell("C" . $row)->getValue();
                    ReadExcelClassroom::addToDB($numberClassroom, $building, $faculty, $numberDepartment);
                } else break;
            }
        }
    }

    public static function addToDB($numberClassroom, $building, $faculty, $numberDepartment){
        ($faculty != null) ? $faculty_id = Faculty::where('shortNameFaculty',$faculty)->value('id')
            : $faculty_id = null;
        ($numberDepartment != null)
            ? $department_id = Department::where('numberDepartment',$numberDepartment)->value('id')
            : $department_id = null;
        Classroom::firstOrCreate([
            'numberClassroom' => $numberClassroom,
        ],[
            'numberClassroom' => $numberClassroom,
            'building' => $building,
            'faculty_id' => $faculty_id,
            'department_id' => $department_id,
        ]);
    }
}
