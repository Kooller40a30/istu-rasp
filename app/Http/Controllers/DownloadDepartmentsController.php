<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Teacher;
use App\ReadExcel\GetPath;
use App\ReadExcel\ReadExcelClassroom;
use App\ReadExcel\ReadExcelDepartment;
use App\ReadExcel\ReadExcelTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadDepartmentsController extends Controller
{
    public function index(){
        return view('download_department');
    }

    public function readDepartment(FileRequest $request){
        set_time_limit(0);
        $file = $request->file('import_file');

        $path = GetPath::savePath($file);

        ReadExcelDepartment::readFile($path);
        //return response()->json(['success'=>'Form is successfully submitted!']);
    }
}
