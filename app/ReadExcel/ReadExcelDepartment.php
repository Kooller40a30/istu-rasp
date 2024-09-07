<?php

namespace App\ReadExcel;


use App\Models\Classroom;
use App\Models\Department;
use App\Models\DepartmentTeacher;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Teacher;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ReadExcelDepartment
{
    public static function readFile($file)
    {
        Schedule::where('id', '>', 0)->delete();
        DepartmentTeacher::where('id', '>', 0)->delete();
        Teacher::where('id', '>', 0)->delete();
        Department::where('id', '>', 0)->delete();
        Classroom::where('id', '>', 0)->delete();
        Department::where('id', '>', 0)->delete();
        $reader = IOFactory::createReaderForFile($file);
        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($file);

        //взять лист
        $sheet = $spreadsheet->getActiveSheet();

        //пройтись по строкам
        $highestRow = $sheet->getHighestRow();
        for ($row = 3; $row <= $highestRow; $row++) {
            $nameDepartment = $sheet->getCell("D" . $row)->getValue();
            if ($nameDepartment != null){
                $shortNameDepartment = $sheet->getCell("E" . $row)->getValue();
                $numberDepartment = $sheet->getCell("C" . $row)->getValue();
                $faculty = $sheet->getCell("F" . $row)->getValue();
                ReadExcelDepartment::addToDB($nameDepartment, $shortNameDepartment, $numberDepartment, $faculty);
            } else break;
        }
    }

    public static function addToDB($nameDepartment, $shortNameDepartment, $numberDepartment, $faculty){
        $faculty_id = Faculty::where('nameFaculty',$faculty)->value('id');
        Department::firstOrCreate([
            'nameDepartment' => $nameDepartment,
        ],[
            'nameDepartment' => $nameDepartment,
            'shortNameDepartment' => $shortNameDepartment,
            'numberDepartment' => $numberDepartment,
            'faculty_id' => $faculty_id,
        ]);
    }
}
