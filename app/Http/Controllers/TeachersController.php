<?php

namespace App\Http\Controllers;

use App\CreateExcel\CreateExcelFiles;
use App\Helpers\TeacherScheduleHelper;
use App\Http\Requests\DepartmentRequest;
use App\Http\Requests\FacultyRequest;
use App\Http\Requests\TeacherRequest;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Teacher;
use App\Services\GetFromDatabase\GetDepartments;
use App\Services\GetFromDatabase\GetFaculties;
use App\Services\GetFromDatabase\GetTeachers;
use Illuminate\Http\Request;

class TeachersController extends Controller
{
    public function getAllTeachers(){
        //Storage::deleteDirectory('public/schedule');
        //Storage::createDirectory('public/schedule');
        $faculties = GetFaculties::findFacultiesForTeachers();
        $departments = GetDepartments::findTeachersDepartments();
        $teachers = GetTeachers::findTeachers();
        $faculty_id = 0;
        $department_id = 0;
        $teacher_id = 0;
        return view('teachers_schedule',compact('faculties','departments','teachers','faculty_id','department_id','teacher_id'));
    }
    public function getFacultyTeachers(FacultyRequest $request){
        set_time_limit(0);
        $faculty_id = $request['faculty'];
        if(isset($_POST['download'])) {
            //Storage::createDirectory('public_html/schedule');
            $path = CreateExcelFiles::createFacultyTeachersExcel($faculty_id);
            return response()->download($path);

        } elseif(isset($_POST['departments'])) {
            $faculties = GetFaculties::findFacultiesForTeachers();
            $departments = GetDepartments::findTeachersDepartments($faculty_id);
            $teachers = GetTeachers::findTeachers($faculty_id);
            $department_id = 0;
            $teacher_id = 0;
            return view('teachers_schedule',compact('faculties','departments','teachers','faculty_id','department_id','teacher_id'));
        }
    }

    public function getDepartmentTeachers(DepartmentRequest $request){
        set_time_limit(0);
        $department_id = $request['department'];
        if(isset($_POST['download'])) {
            //Storage::createDirectory('public_html/schedule');
            $paths = CreateExcelFiles::createTeachersExcel($department_id);
            return response()->download($paths[0]);
        } else {
            $faculty_id = Department::find($department_id)->getFaculty->id;
            $teachers = GetTeachers::findTeachers($faculty_id, $department_id);
            $faculties = GetFaculties::findFacultiesForTeachers();
            $departments = GetDepartments::findTeachersDepartments($faculty_id);
            $teacher_id = 0;
            if (isset($_POST['teachers'])) {
                return response()->view('teachers_schedule', compact('faculties', 'departments', 'teachers', 'faculty_id', 'department_id', 'teacher_id'));
            } elseif (isset($_POST['show'])) {
                $paths = CreateExcelFiles::createTeachersExcel($department_id);
                $html = $paths[1];
                $departmentName = Department::where('id', $department_id)->value('shortNameDepartment');
                $title = 'Расписание кафедры ' . $departmentName;
                return view('teachers_schedule_table', compact('faculties', 'departments', 'teachers', 'faculty_id', 'department_id', 'teacher_id', 'html', 'title'));
            }
        }
    }
    public function getTeacher(TeacherRequest $request){
        set_time_limit(0);
        $teacher_id = $request['teacher'];
        if(isset($_POST['download'])) {
            //Storage::createDirectory('public_html/schedule');
            $paths = CreateExcelFiles::createTeacherExcel($teacher_id);
            return response()->download($paths[0]);
        } elseif(isset($_POST['show'])) {
            $department_id = 0;
            $faculty_id = 0;
            //$departments = Teacher::where('id', $teacher_id)->get();
            //$departments = $departments[0]->getDepartments;
            $departments = Teacher::find($teacher_id)->getDepartments;
            if (count($departments) == 1) {
                $department_id = $departments[0]['id'];
                $faculty_id = Department::where('id',$department_id)->value('faculty_id');
            }
            $faculties = GetFaculties::findFacultiesForTeachers();
            //$departments = GetDepartments::findTeachersDepartments();
            $teachers = GetTeachers::findTeachers();
            $paths = CreateExcelFiles::createTeacherExcel($teacher_id);
            $html = $paths[1];
            $teacherName = Teacher::where('id',$teacher_id)->value('shortNameTeacher');
            $title = 'Расписание преподавателя ' . $teacherName;
            return view('teachers_schedule_table',compact('faculties','departments','teachers','faculty_id','department_id','teacher_id','html','title'));
        }
    }

    public function getTeachers(Request $request)
    {
        $faculty_id = (int)$request->query('faculty');
        $dep_id = (int)$request->query('dep');
        $teachers = GetTeachers::findTeachers($faculty_id, $dep_id);
        $html = '<option value="">Все преподаватели</option>';
        foreach ($teachers as $teacher) {
            $id = $teacher['id'];
            $name = $teacher['shortNameTeacher'];
            $html .= "<option value=\"$id\">$name</option>";
        }
        return response($html);
    }

    public function loadTeacherSchedule(Request $request)
    {
        $facultyId = (int) $request->query('faculty', 0);
        $depId     = (int) $request->query('department', 0);
        $teacherId = (int) $request->query('teacher', 0);
    
        $faculty = $facultyId ? Faculty::find($facultyId) : null;
        $dep     = $depId     ? Department::find($depId)   : null;
        $teacher = $teacherId ? Teacher::find($teacherId)  : null;
    
        $facultyName  = $faculty?->shortNameFaculty  ?? 'Все институты';
        $depName      = $dep?->nameDepartment        ?? 'Все кафедры';
        $teacherName  = $teacher?->shortNameTeacher  ?? 'Все преподаватели';
    
        $header = "Институт: {$facultyName}<br>Кафедра: {$depName}<br>Преподаватель: {$teacherName}";
    
        if ($teacher) {
            $result = TeacherScheduleHelper::generateSchedule($teacher->schedules(), $teacherName);
        } else {
            $result = TeacherScheduleHelper::generateTeachersSchedule($faculty, $dep);
        }
    
        return response()->view('result_schedule', compact('result', 'header'));
    }
    
}