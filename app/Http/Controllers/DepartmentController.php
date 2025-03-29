<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GetFromDatabase\GetDepartments;

class DepartmentController extends Controller
{
    public function getDepartments(Request $request)
    {
        $faculty = (int)$request->query('faculty');
        $departments = GetDepartments::findTeachersDepartments($faculty);
        $html = '<option value="">Все кафедры</option>';

        if ($request->query('for_room', 0)) {
            $html .= '<option value="-1">Без кафедры</option>';
        }

        foreach ($departments as $department) {
            $html .= "<option value=\"{$department->id}\">{$department->nameDepartment}</option>";
        }

        return response($html);
    }
}
