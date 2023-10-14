<?php

namespace App\Http\Controllers;

use App\CreateExcel\ExcelErrors;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ErrorsController extends Controller
{
    public function index(){
        $path = ExcelErrors::createErrors();
        return response()->download($path);
    }
}
