<?php

namespace App\ReadExcel;

use App\Models\Faculty;
use App\Models\Group;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
