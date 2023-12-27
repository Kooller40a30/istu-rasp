<?php

namespace App\ReadExcel;

use App\Models\Department;
use App\Models\DepartmentTeacher;
use App\Models\Teacher;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            if ($nameTeacher != null) {                
                $shortNameTeacher = static::correctNameTeacher($sheet->getCell("B" . $row)->getValue());
                $numberDepartment = $sheet->getCell("D" . $row)->getValue();       
                ReadExcelTeacher::addToDB($nameTeacher, $shortNameTeacher, $numberDepartment);
            } else break;
        }
    }

    protected static function correctNameTeacher($shortNameTeacher)
    {      
        $teacher = mb_convert_encoding($shortNameTeacher, 'UTF-8');
        $patternTeacher = "/^[А-ЯЁ]+\s[А-ЯЁ]+\s[А-ЯЁ]+$/u";
        $teacher = preg_replace_callback($patternTeacher, function($m) {
            $fio = explode(' ', $m[0] ?? []);
            $s = mb_substr($fio[0], 0, 1);            
            $s = $s.mb_strtolower(mb_substr($fio[0], 1));
            $n = $fio[1];
            $p = $fio[2];
            return "{$s} {$n}.{$p}.";
        }, $teacher); 
        
        // если ФИО осталось без изменений
        if (stripos($teacher, '.') !== false) {
            return $teacher;
        }

        $matches = [];  
        preg_match_all("/\w+/ui", $teacher, $matches);
        $matches = $matches[0] ?? [];
        $count = count($matches);

        $teacher = "";
        foreach ($matches as $k => $match) {            
            $subname = mb_strtolower($match);
            $teacher .= mb_strtoupper(mb_substr($subname, 0, 1)) . mb_substr($subname, 1);
            if($count - 1 > $k) {
                $teacher .= ' ';
            }
        }
        
        return $teacher;
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
