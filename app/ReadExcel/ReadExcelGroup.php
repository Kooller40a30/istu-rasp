<?php

namespace App\ReadExcel;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Department;
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

class ReadExcelGroup
{
    public static function readFile($file)
    {
        Group::where('id', '>', 0)->delete();
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
                $nameGroup = $sheet->getCell("A" . $row)->getValue();
                if ($nameGroup != null) {
                    $course = $sheet->getCell("B" . $row)->getValue();
                    $faculty = $sheet->getCell("C" . $row)->getValue();
                    ReadExcelGroup::addToDB($nameGroup, $course, $faculty);
                } else break;
            }
        }
    }

    public static function addToDB($nameGroup, $course, $faculty){
        $faculty_id = Faculty::where('nameFaculty',$faculty)->value('id');
        Group::firstOrCreate([
            'nameGroup' => $nameGroup,
        ],[
            'nameGroup' => $nameGroup,
            'course' => $course,
            'faculty_id' => $faculty_id,
        ]);
    }
}
