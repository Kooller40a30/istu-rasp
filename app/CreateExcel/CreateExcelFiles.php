<?php

namespace App\CreateExcel;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

abstract class CreateExcelFiles
{
    public static function createTeachersExcel($department_id){

        $departments = Department::where('id',$department_id)->get();
        $teachers = $departments[0]->teachers;
        $department = $departments[0]['shortNameDepartment'];
        return CreateExcelFiles::createExcelTeachersOrClassrooms($teachers, $department, false);
    }

    public static function createClassroomsExcel($department_id,$faculty_id = null)
    {
        if ($faculty_id == 0 && $faculty_id != null) {
            $classrooms = Classroom::where('faculty_id', null)->orderBy('numberClassroom')->get();
            $department = 'без факультета';
            //return CreateExcelFiles::createExcelTeachersOrClassrooms($classrooms, $department, true);
        } elseif ($department_id == 0) {
            $classrooms = Classroom::where('department_id', null)
                ->where('faculty_id', $faculty_id)
                ->orderBy('numberClassroom')
                ->get();
            $facultyName = Faculty::where('id',$faculty_id)->value('shortNameFaculty');
            $department = 'без кафедры факультета ' . $facultyName;
            //return CreateExcelFiles::createExcelTeachersOrClassrooms($classrooms, $department, true);
        } else {
            //$departments = Department::where('id', $department_id)->orderBy('numberClassroom')->get();
            //$classrooms = $departments[0]->classrooms;
            $classrooms = Classroom::where('department_id', $department_id)->orderBy('numberClassroom')->get();
            $department = Department::where('id', $department_id)->value('shortNameDepartment');
            //$department = $departments[0]['shortNameDepartment'];
            //return CreateExcelFiles::createExcelTeachersOrClassrooms($classrooms, $department, true);
        }
        return CreateExcelFiles::createExcelTeachersOrClassrooms($classrooms, $department, true);
    }

    public static function createTeacherExcel($teacherId){

        $teachers = Teacher::where('id',$teacherId)->get();
        $schedules = $teachers[0]->schedules;
        $teacher = $teachers[0]['shortNameTeacher'];
        return CreateExcelFiles::createExcelTeacherOrClassroom($schedules, $teacher, false);
    }
    public static function createClassroomExcel($classroomId){

        $classrooms = Classroom::where('id','=',$classroomId)->get();
        $schedules = $classrooms[0]->schedules;
        $classroom = $classrooms[0]['numberClassroom'];
        return CreateExcelFiles::createExcelTeacherOrClassroom($schedules, $classroom, true);
    }

    public static function createFacultyTeachersExcel($faculty_id){

        $faculties = Faculty::where('id',$faculty_id)->get();
        $departments = $faculties[0]->departments;
        $faculty = $faculties[0]['shortNameFaculty'];
        return CreateExcelFiles::createExcelFacultyTeachersOrClassrooms($departments, $faculty, false);
    }

    public static function createFacultyClassroomsExcel($faculty_id)
    {
        if ($faculty_id == 0) {
            $paths = CreateExcelFiles::createClassroomsExcel(null, $faculty_id);
            return $paths[0];
        } else {
            $faculty = Faculty::where('id', $faculty_id)->get();
            $departments = $faculty[0]->departments;
            $facultyName = $faculty[0]['shortNameFaculty'];
            return CreateExcelFiles::createExcelFacultyTeachersOrClassrooms($departments, $facultyName, true, $faculty_id);
        }
    }

    public static function createExcelFacultyTeachersOrClassrooms($departments, $faculty, $isClassrooms, $faculty_id = null){

        $filePattern = 'app/public/patterns/pattern.xlsx';
        $storage_path = storage_path($filePattern);
        $reader = IOFactory::createReaderForFile($storage_path);
        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($storage_path);

        //вставляем листы
        $sheets = [];
        $indexSheets = 0;
        $sheets[$indexSheets] = clone $spreadsheet->getActiveSheet()->setTitle('Без кафедры');
        $indexSheets = 1;
        foreach ($departments as $department){
            $sheets[$indexSheets++] = clone $spreadsheet->getActiveSheet()->setTitle($department['shortNameDepartment']);
            //$indexSheets++;
        }
        for ($i = 0; $i < (count($sheets)-1); $i++){
            $spreadsheet->addSheet($sheets[$i]);
        }

        $indexNo = 0;

        //количество листов
        $sheetCount = $spreadsheet->getSheetCount();

        for ($i = 0; $i < $sheetCount; $i++) {
            //взять лист
            $sheet = $spreadsheet->getSheet($i);
            $titleSheet = $sheet->getTitle();
            if ($titleSheet == 'Без кафедры') {
                if ($isClassrooms) {
                    $classrooms = Classroom::where('faculty_id',$faculty_id)->where('department_id',null)->get();
                    CreateExcelFiles::createSheetTeachersOrClassrooms($sheet,$classrooms,$isClassrooms);
                }else {$indexNo = $i;}
            } else{
                $department = Department::where('shortNameDepartment', $titleSheet)->get();
                ($isClassrooms) ? $teachersOrClassrooms = $department[0]->classrooms
                    : $teachersOrClassrooms = $department[0]->teachers;

                CreateExcelFiles::createSheetTeachersOrClassrooms($sheet,$teachersOrClassrooms,$isClassrooms);
            }
        }
        if (!$isClassrooms) {$spreadsheet->removeSheetByIndex($indexNo);}

        $writer = new Xlsx($spreadsheet);
        $isClassrooms ? $filename = ('Расписание аудиторий факультета ' . $faculty):
            $filename = ('Расписание преподавателей факультета ' . $faculty);

        $filePath = 'app//public//schedule//' . $filename . '.xlsx';
        //$filePath = $filename . '.xlsx';
        $path = storage_path($filePath);
        $writer->save($path);

        return $path;
    }

    public static function createExcelTeachersOrClassrooms($teachersOrClassrooms, $nameDepartmentOrBuilding, $isClassrooms){

        $filePattern = 'app/public/patterns/pattern.xlsx';
        $storage_path = storage_path($filePattern);
        $reader = IOFactory::createReaderForFile($storage_path);
        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($storage_path);

        //Получаем текущий активный лист
        //$sheet = $spreadsheet->getActiveSheet()->setTitle($nameDepartmentOrBuilding);
        $sheet = $spreadsheet->getActiveSheet();

        CreateExcelFiles::createSheetTeachersOrClassrooms($sheet,$teachersOrClassrooms,$isClassrooms);

        $writer = new Xlsx($spreadsheet);
        $isClassrooms ? $filename = ('Расписание аудиторий кафедры ' . $nameDepartmentOrBuilding):
            $filename = ('Расписание преподавателей кафедры ' . $nameDepartmentOrBuilding);

        $filePath = 'app//public//schedule//' . $filename . '.xlsx';
        $path = storage_path($filePath);
        $writer->save($path);

        $filePath = 'app//public//schedule//' . $filename . '.php';
        $path2 = storage_path($filePath);
        $writer2 = IOFactory::createWriter($spreadsheet, 'Html');
        $writer2->save($path2);

        $paths[0] = $path;
        $paths[1] = $path2;

        return $paths;
    }

    public static function createExcelTeacherOrClassroom($schedules, $classroomOrTeacher, $isClassroom){

        $filePattern = 'app/public/patterns/pattern2.xlsx';
        $storage_path = storage_path($filePattern);
        $reader = IOFactory::createReaderForFile($storage_path);
        // Читаем файл и записываем информацию в переменную
        $spreadsheet = $reader->load($storage_path);

        //Получаем текущий активный лист
        $sheet = $spreadsheet->getActiveSheet();
        //$sheet = $spreadsheet->getActiveSheet()->setTitle($classroomOrTeacher);
        $col = 'A';
        $sheet->setCellValue('A1', $classroomOrTeacher);

        foreach ($schedules as $schedule) {
            //данные
            $day = $schedule['day'];
            $week = $schedule['week'];
            $class = $schedule['class'];
            //dd($schedule['classroom_id']);
            if ($isClassroom) {
                ($schedule['teacher_id'] != null) ? $teacherOrClassroom = $schedule->getTeacher['shortNameTeacher']
                    : $teacherOrClassroom = '';
            } else {
                ($schedule['classroom_id'] != null) ? $teacherOrClassroom = $schedule->getClassroom['numberClassroom']
                    : $teacherOrClassroom = '';
            }
            $discipline = $schedule['discipline'];
            $group = $schedule->getGroup['nameGroup'];

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
            //значение ячейки
            $value = $discipline . ', ' . $teacherOrClassroom . ', ' . $group;

            $sheet->getColumnDimension($col)->setWidth(30);
            $sheet->getRowDimension($row)->setRowHeight(35);
            //проверка на лекцию
            $valueCell = $sheet->getCell($cell)->getValue();
            if ($valueCell != null) {
                $value = $valueCell . ' ' . $group;
            }
            //записать полученное значение в ячейку
            $sheet->setCellValue($cell, $value);
        }

        $writer = new Xlsx($spreadsheet);
        $classroomOrTeacher = str_replace('/','.',$classroomOrTeacher);
        $isClassroom ? $filename = ('Расписание аудитории ' . $classroomOrTeacher) :
            $filename = ('Расписание преподавателя ' . $classroomOrTeacher);
        
        $filePath = 'app//public//schedule//' . $filename. '.xlsx';
        //$filePath = $filename. '.xlsx';
        $path = storage_path($filePath);
        $writer->save($path);

        $filePath = 'app//public//schedule//' . $filename . '.php';
        $path2 = storage_path($filePath);
        $writer2 = IOFactory::createWriter($spreadsheet, 'Html');
        $writer2->save($path2);

        $paths[0] = $path;
        $paths[1] = $path2;

        return $paths;
    }

    public static function createSheetTeachersOrClassrooms(&$sheet, $teachersOrClassrooms, $isClassrooms){
        $col = 'D';
        foreach ($teachersOrClassrooms as $teacherOrClassroom){
            //получаем расписание
            $schedules = $teacherOrClassroom->schedules;
            //если у преподавателя или аудитории есть расписание
            if (isset($schedules[0])) {
                //записываем в ячейку преподавателя или аудиторию
                $isClassrooms ? ($sheet->setCellValue($col . '1', $teacherOrClassroom['numberClassroom'])) :
                    ($sheet->setCellValue($col . '1', $teacherOrClassroom['shortNameTeacher']));
                foreach ($schedules as $schedule) {
                    //данные
                    $day = $schedule['day'];
                    $week = $schedule['week'];
                    $class = $schedule['class'];
                    if ($isClassrooms) {
                        ($schedule['teacher_id'] != null) ? $nameTeacherOrClassroom = $schedule->getTeacher['shortNameTeacher']
                            : $nameTeacherOrClassroom = '';
                    } else {
                        ($schedule['classroom_id'] != null) ? $nameTeacherOrClassroom = $schedule->getClassroom['numberClassroom']
                            : $nameTeacherOrClassroom = '';
                    }
                    $discipline = $schedule['discipline'];
                    $group = $schedule->getGroup['nameGroup'];

                    //получаем нужную строку
                    $row = ($day - 1) * 14 + 2 * $class + $week - 1;

                    $cell = $col . $row;
                    //значение ячейки
                    $value = $discipline . ', ' . $nameTeacherOrClassroom . ', ' . $group;

                    //если несколько групп (лекция и т д)
                    $valueCell = $sheet->getCell($cell)->getValue();
                    if ($valueCell != null) {
                        $value = $valueCell . ' ' . $group;
                    }

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