<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Teacher;
use App\Services\GetFromDatabase\CoursesRepository;
use App\Services\GetFromDatabase\GetDepartments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Services\GetFromDatabase\GetFaculties;
use App\Services\GetFromDatabase\GetGroups;
use App\Services\GetFromDatabase\GetGroupsCourses;

class StartController extends Controller
{
    public function index(Request $request)
    {
        Storage::deleteDirectory('public_html/schedule');
        $files = collect(Storage::allFiles('public//schedule'));
        $files->each(function ($file) {
            $lastModified = Storage::lastModified($file);
            $lastModified = Carbon::parse($lastModified);

            if (Carbon::now()->gt($lastModified->addHour(12))) {
                Storage::delete($file);
            }
        });
        $facultiesGroup = GetFaculties::facultiesToGroups();
        $facultiesTeacher = GetFaculties::facultiesToTeachers();
        $facultiesRoom = GetFaculties::facultiesToClassrooms();
        $courses = CoursesRepository::findAll();
        $groups = GetGroups::groups();
        $deps = Department::get();
        $teachers = Teacher::get();
        $classrooms = Classroom::get();
        $result = "";        
        
        return view('main_page', compact('facultiesGroup', 'facultiesTeacher', 'facultiesRoom', 
                    'teachers', 'deps', 'classrooms', 'courses', 'groups', 'result'));
    }
}