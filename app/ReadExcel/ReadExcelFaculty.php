<?php

namespace App\ReadExcel;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\DepartmentTeacher;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReadExcelFaculty
{
    public static function readFile($file)
    {
        Schedule::where('id', '>', 0)->delete();
        Group::where('id', '>', 0)->delete();
        DepartmentTeacher::where('id', '>', 0)->delete();
        Teacher::where('id', '>', 0)->delete();
        Department::where('id', '>', 0)->delete();
        Classroom::where('id', '>', 0)->delete();
        Faculty::where('id', '>', 0)->delete();
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
                $nameFaculty = $sheet->getCell("A" . $row)->getValue();
                if ($nameFaculty != null){
                    $shortNameFaculty = $sheet->getCell("B" . $row)->getValue();
                    ReadExcelFaculty::addToDB($nameFaculty,$shortNameFaculty);
                } else break;
            }
        }
    }

    public static function addToDB($nameFaculty,$shortNameFaculty){
        Faculty::firstOrCreate([
            'nameFaculty' => $nameFaculty,
        ],[
            'nameFaculty' => $nameFaculty,
            'shortNameFaculty' => $shortNameFaculty,
        ]);
    }
}
