<?php

namespace App\Http\Controllers;

use App\Services\GetFromDatabase\CoursesRepository;
use Illuminate\Http\Request;

class CoursesController extends Controller 
{
    public function getCourses(Request $request)
    {
        $faculty = (int)$request->query('faculty');        
        $list = static::getList($faculty);
        return response($list);
    }

    public static function getList($faculty)
    {
        $courses = CoursesRepository::findAll($faculty);
        $list = '<option value="">Все курсы</option>';
        foreach ($courses as $course) {
            $id = $course['id'];
            $name = $course['nameCourse'];
            $list .= "<option value=\"$id\">$name</option>";
        }
        return $list;
    }
}