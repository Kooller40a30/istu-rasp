<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Services\GetFromDatabase\GetFaculties;
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
        $faculty_id = $request->input('faculty', 0);
        $group_id = $request->input('group', 0);
        $courseName = $request->input('course', "");
        $faculties = GetFaculties::facultiesToGroups();
        $courses = GetGroupsCourses::courses($faculty_id);
        $groups = [];
        $result = "";        
        
        return view('main_page', compact('faculties', 'courses', 'groups', 'faculty_id', 'group_id', 'courseName', 'result'));
    }
}