<?php

namespace App\Http\Controllers;

use App\CreateExcel\CreateExcelFiles;
use App\Http\Requests\ClassroomRequest;
use App\Http\Requests\DepartmentRequest;
use App\Http\Requests\FacultyRequest;
use App\Http\Requests\TeacherRequest;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Services\GetFromDatabase\GetClassrooms;
use App\Services\GetFromDatabase\GetDepartments;
use App\Services\GetFromDatabase\GetFaculties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClassroomsController extends Controller
{
    public function getAllClassrooms(){
        //Storage::deleteDirectory('publiс/schedule');
        //Storage::createDirectory('public/schedule');
        $faculties = GetFaculties::facultiesToClassrooms();
        $departments = GetDepartments::classroomsDepartments();
        $classrooms = GetClassrooms::classrooms();
        $faculty_id = -1;
        $department_id = -1;
        $classroom_id = -1;
        return view('classrooms_schedule',compact('faculties','departments','classrooms','faculty_id','department_id','classroom_id'));
    }
    public function getFacultyClassrooms(FacultyRequest $request){
        set_time_limit(0);
        $faculty_id = $request['faculty'];
        if(isset($_POST['download_faculty'])) {
            //Storage::createDirectory('public_html/schedule');
            $path = CreateExcelFiles::createFacultyClassroomsExcel($faculty_id);
            return response()->download($path);
        } elseif(isset($_POST['departments'])) {
            $faculties = GetFaculties::facultiesToClassrooms();
            //$departments = GetDepartments::departmentsFacultyToClassrooms($faculty_id);
            $departments = GetDepartments::classroomsDepartments($faculty_id);
            $classrooms = GetClassrooms::classrooms($faculty_id);
            $department_id = -1;
            $classroom_id = -1;
            return view('classrooms_schedule',compact('faculties','departments','classrooms','faculty_id','department_id','classroom_id'));
        }
    }

    public function getDepartmentClassrooms(DepartmentRequest $request){
        set_time_limit(0);
        //$department_id = $request['department'];
        $department = explode('.', $_POST['department']);
        $department_id = $department[0];
        $faculty_id = $department[1];
        //dd($department);
        if(isset($_POST['download_department'])) {
            //Storage::createDirectory('public_html/schedule');
            $paths = CreateExcelFiles::createClassroomsExcel($department_id,$faculty_id);
            return response()->download($paths[0]);
        } else {
            //$faculty_id = Department::find($department_id)->getFaculty->id;
            $classrooms = GetClassrooms::classrooms($faculty_id, $department_id);
            $faculties = GetFaculties::facultiesToClassrooms();
            //$departments = GetDepartments::departmentsFacultyToClassrooms($faculty_id);
            $departments = GetDepartments::classroomsDepartments($faculty_id);
            $classroom_id = -1;
            if (isset($_POST['classrooms'])) {
                return response()->view('classrooms_schedule', compact('faculties', 'departments', 'classrooms', 'faculty_id', 'department_id','classroom_id'));
            } elseif (isset($_POST['show_department'])) {
                $paths = CreateExcelFiles::createClassroomsExcel($department_id,$faculty_id);
                $html = $paths[1];
                if ($department_id == 0) {
                    $facultyName = Faculty::where('id', $faculty_id)->value('shortNameFaculty');
                    $title = 'Расписание без кафедры факультета ' . $facultyName;
                } else {
                    $departmentName = Department::where('id', $department_id)->value('shortNameDepartment');
                    $title = 'Расписание кафедры ' . $departmentName;
                }
                return view('classrooms_schedule_table', compact('faculties', 'departments', 'classrooms', 'faculty_id', 'department_id', 'classroom_id', 'html', 'title'));
            }
        }
    }
    public function getClassroom(ClassroomRequest $request){
        set_time_limit(0);
        //$classroom_id = $request['classroom'];
        $classroom = explode('.', $_POST['classroom']);
        $classroom_id = $classroom[0];
        if(isset($_POST['download'])) {
            //Storage::createDirectory('public_html/schedule');
            $paths = CreateExcelFiles::createClassroomExcel($classroom_id);
            return response()->download($paths[0]);
        } elseif(isset($_POST['show'])) {
            $department_id = $classroom[2];
            $faculty_id = $classroom[1];

            $faculties = GetFaculties::facultiesToClassrooms();
            $departments = GetDepartments::classroomsDepartments($faculty_id);
            $classrooms = GetClassrooms::classrooms($faculty_id,$department_id);
            $paths = CreateExcelFiles::createClassroomExcel($classroom_id);
            $html = $paths[1];
            $classroomName = Classroom::where('id',$classroom_id)->value('numberClassroom');
            $title = 'Расписание аудитории ' . $classroomName;
            return view('classrooms_schedule_table',compact('faculties','departments','classrooms','faculty_id','department_id','classroom_id','html','title'));
        }
    }

    public function getClassrooms(Request $request)
    {
        $faculty_id = (int)$request->query('faculty', 0);
        $dep_id = (int)$request->query('dep', 0);
        $classrooms = GetClassrooms::classrooms($faculty_id, $dep_id);
        $html = '<option value="">Все аудитории</option>';
        foreach ($classrooms as $room) {
            $id = $room['id'];
            $name = $room['numberClassroom'];
            $html .= "<option value=\"$id\">$name</option>";
        }
        return response($html);
    }

    public function loadClassroomSchedule(ClassroomRequest $request) 
    {
        
    }
}