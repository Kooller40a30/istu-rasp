<?php

namespace App\ReadExcel;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\DepartmentTeacher;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReadExcelTeacher
{
    public static function readFile($file)
    {
        DepartmentTeacher::where('id', '>', 0)->delete();
        Teacher::where('id', '>', 0)->delete();
        $reader = IOFactory::createReaderForFile($file);
        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($file);

        //взять лист
        $sheet = $spreadsheet->getActiveSheet();

        //пройтись по строкам
        $highestRow = $sheet->getHighestRow();
        for ($row = 3; $row <= $highestRow; $row++) {
            $nameTeacher = $sheet->getCell("A" . $row)->getValue();
            if ($nameTeacher != null){
                $shortNameTeacher = $sheet->getCell("B" . $row)->getValue();
                $numberDepartment = $sheet->getCell("D" . $row)->getValue();

                ReadExcelTeacher::addToDB($nameTeacher, $shortNameTeacher, $numberDepartment);
            } else break;
        }
    }

    public static function addToDB($nameTeacher, $shortNameTeacher, $numberDepartment){
        Teacher::firstOrCreate([
            'nameTeacher' => $nameTeacher,
        ],[
            'nameTeacher' => $nameTeacher,
            'shortNameTeacher' => $shortNameTeacher,
        ]);
        $teacher_id = Teacher::where('nameTeacher',$nameTeacher)->value('id');
        $department_id = Department::where('numberDepartment',$numberDepartment)->value('id');
        if ($department_id != null) {
            DepartmentTeacher::firstOrCreate([
                'teacher_id' => $teacher_id,
                'department_id' => $department_id,
            ], [
                'teacher_id' => $teacher_id,
                'department_id' => $department_id,
            ]);
        }
    }
}
