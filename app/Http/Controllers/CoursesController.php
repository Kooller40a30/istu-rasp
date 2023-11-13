<?php

namespace App\Http\Controllers;

use App\Services\GetFromDatabase\CoursesRepository;
use Illuminate\Http\Request;

class CoursesController extends Controller 
{
    public function getCourses(Request $request)
    {
        $matches = [];
        preg_match('/\d+/ui', $request['faculty'], $matches);
        $courses = CoursesRepository::findAll($matches[0] ?? 0);
        $list = '';
        foreach ($courses as $course) {
            $id = $course['id'];
            $name = $course['nameCourse'];
            $list .= "<option value=\"$id\">$name</option>";
        }
        return response($list);
    }
}