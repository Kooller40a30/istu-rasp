<?php

namespace App\Http\Controllers;

use App\CreateExcel\CreateExcelFiles;
use App\Http\Requests\DepartmentRequest;
use App\Http\Requests\FacultyRequest;
use App\Http\Requests\TeacherRequest;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\DepartmentTeacher;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Services\GetFromDatabase\GetDepartments;
use App\Services\GetFromDatabase\GetFaculties;
use App\Services\GetFromDatabase\GetTeachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeachersController extends Controller
{
    public function getAllTeachers(){
        //Storage::deleteDirectory('public/schedule');
        //Storage::createDirectory('public/schedule');
        $faculties = GetFaculties::facultiesToTeachers();
        $departments = GetDepartments::teachersDepartments();
        $teachers = GetTeachers::teachers();
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
            $faculties = GetFaculties::facultiesToTeachers();
            $departments = GetDepartments::teachersDepartments($faculty_id);
            $teachers = GetTeachers::teachers($faculty_id);
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
            $teachers = GetTeachers::teachers($faculty_id, $department_id);
            $faculties = GetFaculties::facultiesToTeachers();
            $departments = GetDepartments::teachersDepartments($faculty_id);
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
            $faculties = GetFaculties::facultiesToTeachers();
            //$departments = GetDepartments::teachersDepartments();
            $teachers = GetTeachers::teachers();
            $paths = CreateExcelFiles::createTeacherExcel($teacher_id);
            $html = $paths[1];
            $teacherName = Teacher::where('id',$teacher_id)->value('nameTeacher');
            $title = 'Расписание преподавателя ' . $teacherName;
            return view('teachers_schedule_table',compact('faculties','departments','teachers','faculty_id','department_id','teacher_id','html','title'));
        }
    }
}