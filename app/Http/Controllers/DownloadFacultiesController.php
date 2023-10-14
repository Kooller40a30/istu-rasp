<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Teacher;
use App\ReadExcel\GetPath;
use App\ReadExcel\ReadExcelClassroom;
use App\ReadExcel\ReadExcelFaculty;
use App\ReadExcel\ReadExcelTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class DownloadFacultiesController extends Controller
{
    public function index(){
        return view('download_faculty');
    }

    public function readFaculty(FileRequest $request){
        set_time_limit(0);
        $file = $request->file('import_file');

        $path = GetPath::savePath($file);

        ReadExcelFaculty::readFile($path);
        //return view('download_faculty');
        //return response()->json(['success'=>'Form is successfully submitted!']);
    }
}