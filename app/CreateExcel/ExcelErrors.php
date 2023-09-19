<?php

namespace App\CreateExcel;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Error;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelErrors
{
    public static function createErrors(){
        $filePattern = 'app/public/patterns/patternError.xlsx';
        $storage_path = storage_path($filePattern);
        $reader = IOFactory::createReaderForFile($storage_path);
        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($storage_path);

        $j = 1;
        $sheets = [];

        for ($i = 0; $i < 11; $i++){
            $sheets[$i] = clone $spreadsheet->getActiveSheet()->setTitle((string)($i+2));
            //$workSheet = $newSheet->setTitle((string)$i);
            //$workSheet = $newSheet;
            //$spreadsheet->addSheet($sheets[$i]);
        }
        for ($i = 0; $i < 10; $i++){
            $spreadsheet->addSheet($sheets[$i]);
        }
        //dd($spreadsheet->getSheetCount());
        $sheet = $spreadsheet->getActiveSheet()->setTitle((string)$j);
        $errors = Error::all();
        $row = 2;
        foreach ($errors as $error){
            if (isset($file) && $file != $error['file']) {
                $j++;
                $sheet = $spreadsheet->getSheet($j-1);
                $row = 2;
            }
            $file = $error['file'];
            $group = $error['group'];
            $day = $error['day'];
            switch ($day) {
                case 1: $day = 'понедельник'; break;
                case 2: $day = 'вторник'; break;
                case 3: $day = 'среда'; break;
                case 4: $day = 'четверг'; break;
                case 5: $day = 'пятница'; break;
                case 6: $day = 'суббота'; break;
            }
            $week = $error['week'];
            switch ($week) {
                case 1: $week = 'над чертой'; break;
                case 2: $week = 'под чертой'; break;
            }
            $class = $error['class'];
            $value = $error['value'];
            $teacher = $error['teacher'];
            $classroom = $error['classroom'];
            $discipline = $error['discipline'];

            $sheet->setCellValue('A' . $row, $file);
            $sheet->setCellValue('B' . $row, $group);
            $sheet->setCellValue('C' . $row, $day);
            $sheet->setCellValue('D' . $row, $week);
            $sheet->setCellValue('E' . $row, $class);
            $sheet->setCellValue('F' . $row, $value);
            $sheet->setCellValue('G' . $row, $teacher);
            $sheet->setCellValue('H' . $row, $classroom);
            $sheet->setCellValue('I' . $row, $discipline);

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Ошибки';
        //$filePath = 'app\public\excel' . $filename . '.xlsx';
	$filePath = $filename . '.xlsx';
        $path = storage_path($filePath);
        $writer->save($path);
        return $path;
    }
}