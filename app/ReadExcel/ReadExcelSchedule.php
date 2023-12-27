<?php

namespace App\ReadExcel;

use App\Models\Classroom;
use App\Models\Error;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\Teacher;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ReadExcelSchedule
{
    public static function readFiles($files){
        Schedule::where('id', '>', 0)->delete();
        Error::where('id', '>', 0)->delete();
        foreach ($files as $file){
            $filename = $file->getClientOriginalName();
            $path = GetPath::savePath($file);
            ReadExcelSchedule::readFile($path, $filename);
        }
    }

    public static function readFile($file, $filename)
    {
        $reader = IOFactory::createReaderForFile($file);
        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($file);
        $sheetCount = $spreadsheet->getSheetCount();

        for ($i = 0; $i < $sheetCount; $i++) {
            //взять лист
            $sheet = $spreadsheet->getSheet($i);

            //найти начальную ячейку со значением группа
            $startCol = '';
            $startRow = '';
            for ($col = "A"; $col < "D"; $col++) {
                for ($row = 1; $row < 5; $row++) {
                    if ($sheet->getCell($col . $row)->getValue() === 'группа') {
                        $startCol = $col;
                        $startCol++;
                        $startCol++;
                        $startRow = $row;
                        break 2;
                    }
                }
            }

            if ($startRow === '') {
                $error = 'Не найден файл с групповым расписанием';
                return view('download_schedules',compact('error'));
            }

            //проходим по столбцам
            $highestColumn = $sheet->getHighestColumn();
            for ($col = $startCol; $col <= $highestColumn; $col++) {
                //получить ячейку
                $cell = $sheet->getCell($col . $startRow);

                //проверка на объединенные ячейки
                $cellMerge = ReadExcelSchedule::isMergeCell($sheet, $cell);
                $cellMerge ? $group = $cellMerge->getValue() : $group = $cell->getValue();

                //проверка на подгруппу
                $isSubgroup = ReadExcelSchedule::isSubgroup($group);
                if ($isSubgroup) {
                    $newStartRow = $startRow - 1;
                    $newCell = $sheet->getCell($col . $newStartRow);
                    $cellMerge = ReadExcelSchedule::isMergeCell($sheet, $newCell);
                    $cellMerge ? ($group = $cellMerge->getValue() . ' ' . $group) : ($group = $newCell->getValue() . ' ' . $group);
                    //dd($group);
                }

                //проверка на подгруппу
                $cellDown = $sheet->getCell($col . ($startRow+1))->getValue();
                $isSubgroup = ReadExcelSchedule::isSubgroup($cellDown);
                if ($isSubgroup) {
                    $startRow++;
                    $group = $group . ' ' . $cellDown;
                }

                //проходим по строкам
                for ($row = $startRow + 1; $row <= 84 + $startRow; $row++) {

                    //получить ячейку
                    $cell = $sheet->getCell($col . $row);

                    //проверка на объединенные ячейки
                    $cellMerge = ReadExcelSchedule::isMergeCell($sheet, $cell);
                    if ($cellMerge){ $cell = $cellMerge;}

                    //значение внутри ячейки
                    $cellValue = $cell->getValue();

                    //получить значения ячейки
                    $cellValues = ReadExcelSchedule::getValuesFromCell($cellValue);
                    //если значения найдены
                    if ($cellValues != null) {
                        //день недели
                        $day = intdiv($row - $startRow - 1, 14) + 1;
                        //неделя над/под
                        $week = ($row - $startRow) % 2 == 0 ? 2 : 1;
                        //пара
                        $class = ($row - $startRow + 16 - (14 * $day) - $week) / 2;


                        //записать все полученные значения
                        foreach ($cellValues as $value) {

                            $values = [];
                            $values['discipline'] = $value['discipline'];
                            $values['teacher'] = $value['teacher'];
                            $values['classroom'] = $value['classroom'];

                            $values['group'] = mb_strtoupper($group);
                            $values['day'] = $day;
                            $values['week'] = $week;
                            $values['class'] = $class;
                            $values['cellValue'] = $value['valueCell'];

                            ReadExcelSchedule::addToDB($values, $filename);
                        }
                    }
                }
            }
        }
    }

    public static function isMergeCell($sheet, $cell){
        $cellsMerge = $cell->getMergeRange();
        if ($cellsMerge){
            //$cell = $sheet->getCell(preg_replace('/:[A-Z0-9]+/', '', $cellsMerge));
            return $sheet->getCell(preg_replace('/:[A-Z0-9]+/', '', $cellsMerge));
        }
        else {
            return false;
        }
    }
    public static function isSubgroup($group){
        $patternGroup = "/(^[0-2][' ']*подгруппа)|(^[0-2][' ']*погруппа)/u";
        return preg_match($patternGroup, $group) == 1;
    }

    public static function getValuesFromCell($cellValue)
    {
        if ($cellValue == null){
            return  null;
        }
        $newCellValue = preg_replace("/\n/"," ",$cellValue);
        $patternPhysicalCulture = "/([(11)]*[а-яё\\/()+' ']*[' ']*Физическая[' ']*культура[' ']*и[' ']*спорт[' ']*[(][' '().,а-яё]*[)])|([(11)]*[а-яё\\/()+' ']*[' ']*Физическая[' ']*культура[' ']*и[' ']*спорт[' ',]*[(][' ']*лекции[' ']*проводятся[.0-9а-яё' ']+[)])/ui";
        $value = preg_replace($patternPhysicalCulture, '', $newCellValue);
        $value = trim($value);
        $values = [[]];
        //$values[0]['value'] = $value;
        if ($value == '') {
            $values[0]['valueCell'] = $cellValue;
            $values[0]['teacher'] = null;
            $values[0]['classroom'] = null;
            $values[0]['discipline'] = 'Физическая культура и спорт';
            return $values;
        } else {
            //значение ячейки полностью
            $values[0]['valueCell'] = $cellValue;

            $value = str_replace(["ауд.", "аудд."], '', $value);

            //преподаватель
            $patternTeacher = "/[А-ЯЁ][а-яё]+[' ']+[А-ЯЁ][.,' ']+[' ']*[А-ЯЁ][.' ']*/u";
            preg_match($patternTeacher, $value, $matches) == 1 ?
                $values[0]['teacher'] = $matches[0] : $values[0]['teacher'] = null;
            //если преподаватель без инициалов
            if ($values[0]['teacher'] == null){
                $patternTeacher = "/([А-ЯЁ][а-яё]+[,.' ']+[0-9])|(Аль[' ']+Аккад[' ']+Мхд[' ']+Айман)|(Морар[' ']+Габриела)/u";
                preg_match($patternTeacher, $value, $matches) == 1 ?
                    $teacher = $matches[0] : $teacher = null;
                $values[0]['teacher'] = preg_replace("/[,.' ']+[0-9]/u", '', $teacher);
            }
            $value = str_replace($values[0]['teacher'], '', $value);
            //второй преподаватель
            preg_match($patternTeacher, $value, $matches) == 1 ?
                $values[1]['teacher'] = $matches[0] : $values[1]['teacher'] = null;

            //аудитория
            $patternClassroom = "/([0-9][' ']*-[' ']*[0-9а-яё\\/-]+)|([0-9][А-Я]*[' ']*-[' ']*[0-9а-яё\\/-]+)/ui";
            preg_match($patternClassroom, $value, $matches) == 1 ?
                $values[0]['classroom'] = $matches[0] : $values[0]['classroom'] = null;
            $value = str_replace($values[0]['classroom'], '', $value);
            //вторая аудитория
            preg_match($patternClassroom, $value, $matches) == 1 ?
                $values[1]['classroom'] = $matches[0] : $values[1]['classroom'] = null;

            $patternDiscipline = "/[(]*[' ']*[0-9]+[' ']*[)][' ']*[А-ЯЁа-яё0-9A-Za-z()-]+[' ']*[A-Za-zА-Яа-яёЁ0-9.,' '+()\\/-]*[A-Za-zА-ЯЁа-яё]/u";
            $patternDiscipline2 = "/[(]*[' ']*[0-9]+[' ']*[)][' ']*[А-ЯЁа-яё0-9A-Za-z()-]+[' ']*[A-Za-zА-Яа-яёЁ0-9.,' '+()\\/-]*[(][' ']*[0-9]+[' ']*[)]/u";

            //если 2 аудитории
            if ($values[1]['classroom'] != null){
                $values[1]['valueCell'] = $cellValue;
                //если 2 преподавателя
                if ($values[1]['teacher'] != null) {

                    //проверка на исключение (в одной ячейке информация о занятиях по подгруппам)
                    $patternExceptionValue = "/([0-2]ая[' ']*п\\/гр)|([0-2]ая[' ']*подгруппа)/u";
                    if (preg_match($patternExceptionValue, $value, $matches) == 1) {
                        $value = preg_replace($patternExceptionValue, '', $value);
                        $value = str_replace([$values[1]['teacher'], $values[1]['classroom']], '', $value);
                        //$values[0]['value'] = $value;


                        //дисциплина
                        if (preg_match($patternDiscipline, $value, $matches) == 1) {
                            $values[0]['discipline'] = $matches[0];
                            $values[1]['discipline'] = $matches[0];
                        } else {
                            $values[0]['discipline'] = 'не найдено';
                            $values[1]['discipline'] = 'не найдено';
                        }
                    }
                    else {
                        $value = str_replace([$values[1]['teacher'], $values[1]['classroom']], '', $value);

                        //дисциплина
                        preg_match($patternDiscipline2, $value, $matches) == 1 ?
                            $discipline = $matches[0] : $discipline = 'не найдено';
                        $values[0]['discipline'] = preg_replace("/([' ',]*[(][' ']*[0-9]+[' ']*[)])$/u", '', $discipline);
                        $value = str_replace($values[0]['discipline'], '', $value);
                        preg_match($patternDiscipline, $value, $matches) == 1 ?
                            $values[1]['discipline'] = $matches[0] : $values[1]['discipline'] = $values[0]['discipline'];
                        if ($values[0]['discipline'] == 'не найдено') {
                            $values[0]['discipline'] = $values[1]['discipline'];
                        }
                    }
                }
                else {
                    $value = str_replace([$values[1]['classroom']], '', $value);

                    //преподаватель
                    $values[1]['teacher'] =  $values[0]['teacher'];
                    //дисциплина
                    if (preg_match($patternDiscipline, $value, $matches) == 1) {
                        $values[0]['discipline'] = $matches[0];
                        $values[1]['discipline'] = $matches[0];
                    } else {
                        $values[0]['discipline'] = 'не найдено';
                        $values[1]['discipline'] = 'не найдено';
                    }
                }
            } else {
                //дисциплина
                preg_match($patternDiscipline, $value, $matches) == 1 ?
                    $values[0]['discipline'] = $matches[0] : $values[0]['discipline'] = 'не найдено';
            }

            $values[0]['classroom'] = str_replace(" ", '', $values[0]['classroom']);
            $values[1]['classroom'] = str_replace(" ", '', $values[1]['classroom']);
            $values[0]['classroom'] = mb_strtolower($values[0]['classroom']);
            $values[1]['classroom'] = mb_strtolower($values[1]['classroom']);

            $values[0]['teacher'] = preg_replace("/[.]/", ' ', $values[0]['teacher']);
            $values[1]['teacher'] = preg_replace("/[.]/", ' ', $values[1]['teacher']);
            $values[0]['teacher'] = preg_replace("/([' ']{2,})/", ' ', $values[0]['teacher']);
            $values[0]['teacher'] = trim($values[0]['teacher']);
            $values[1]['teacher'] = preg_replace("/([' ']{2,})/", ' ', $values[1]['teacher']);
            $values[1]['teacher'] = trim($values[1]['teacher']);

            if (!isset($values[1]['discipline'])) {
                unset($values[1]);
            } else {
                $values[1]['teacher'] = mb_strtoupper($values[1]['teacher']);
            }
            $values[0]['teacher'] = mb_strtoupper($values[0]['teacher']);
            return $values;
        }
    }

    public static function addToDB($values, $file){

        $teacher_id = Teacher::where('shortNameTeacher',$values['teacher'])->value('id');
        /*if ($teacher_id == null) {
            $teacher = $values['teacher'];
            $teacher_id = Teacher::where('shortNameTeacher','like', "$teacher%")->value('id');
            if ($teacher_id == 1){
                $teacher_id = null;
            }
        }*/
        $classroom_id = Classroom::where('numberClassroom',$values['classroom'])->value('id');
        $group_id = Group::where('nameGroup',$values['group'])->value('id');
        if ($group_id != null){
            Schedule::firstOrCreate([
                'teacher_id' => $teacher_id,
                'day' => $values['day'],
                'week' => $values['week'],
                'class' => $values['class'],
                'group_id' => $group_id,
            ], [
                'teacher_id' => $teacher_id,
                'day' => $values['day'],
                'week' => $values['week'],
                'class' => $values['class'],
                'group_id' => $group_id,
                'discipline' => $values['discipline'],
                'classroom_id' => $classroom_id,
                'content' => $values['cellValue']
            ]);
        }
        ($teacher_id == null) ? $teacher = $teacher_id : $teacher = $values['teacher'];
        ($classroom_id == null) ? $classroom = $classroom_id : $classroom = $values['classroom'];
        if (($teacher_id == null || $classroom_id == null || $values['discipline'] == 'не найдено') && ($values['discipline'] != 'Физическая культура и спорт')) {
            Error::firstOrCreate([
                'file' => $file,
                'day' => $values['day'],
                'week' => $values['week'],
                'class' => $values['class'],
                'group' => $values['group'],
            ], [
                'file' => $file,
                'teacher' => $teacher,
                'day' => $values['day'],
                'week' => $values['week'],
                'class' => $values['class'],
                'group' => $values['group'],
                'discipline' => $values['discipline'],
                'classroom' => $classroom,
                'value' => $values['cellValue']
            ]);
        }
    }
}
