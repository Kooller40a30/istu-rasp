<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Nette\Utils\Json;
use App\Services\GetFromDatabase\GetDepartments;

class DepartmentController extends Controller
{
    public function getDepartments(Request $request)
    {
        $faculty = (int)$request->query('faculty');
        $deps = GetDepartments::teachersDepartments($faculty);
        $html = '<option value="">Все</option>';
        foreach ($deps as $dep) {
            $id = $dep['id'];
            $nameDep = $dep['nameDepartment'];
            $html .= "<option value=\"$id\">$nameDep</option>";
        }
        return response($html);
    }
}