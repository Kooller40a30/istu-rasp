<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Services\GetFromDatabase\GetFaculties;
use App\Models\Group;
use App\ReadExcel\ExcelReader;
use App\Services\GetFromDatabase\GetGroupsCourses;

class StartController extends Controller
{
    public function index(Request $request)
    {
        set_time_limit(0);
        Storage::deleteDirectory('public_html/schedule');
        $files = collect(Storage::allFiles('public//schedule'));
        $files->each(function ($file) {
            $lastModified = Storage::lastModified($file);
            $lastModified = Carbon::parse($lastModified);

            if (Carbon::now()->gt($lastModified->addHour(12))) {
                Storage::delete($file);
            }
        });
        $faculty_id = $request->input('faculty', 0);
        $group_id = $request->input('group', 0);
        $courseName = $request->input('course', "");
        $faculties = GetFaculties::facultiesToGroups();
        $courses = GetGroupsCourses::courses($faculty_id);
        $groups = [];
        $result = "";
        
        /// ------------------------
        /// @todo вынести в отдельный контроллер
        $file = collect(Storage::files('public/schedules'))->first(function($file, $key) {
            return stripos($file, 'input.xls') !== false;
        });
        $excelReader = new ExcelReader(storage_path('app/') . $file);
        $excelReader->processFile();
        /// ------------------------
        
        return view('main_page', compact('faculties', 'courses', 'groups', 'faculty_id', 'group_id', 'courseName', 'result'));
    }
}