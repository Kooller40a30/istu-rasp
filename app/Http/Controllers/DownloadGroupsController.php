<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Teacher;
use App\ReadExcel\GetPath;
use App\ReadExcel\ReadExcelClassroom;
use App\ReadExcel\ReadExcelGroup;
use App\ReadExcel\ReadExcelTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadGroupsController extends Controller
{
    public function index(){
        return view('download_group');
    }

    public function readGroups(FileRequest $request){
        set_time_limit(0);
        $file = $request->file('import_file');

        $path = GetPath::savePath($file);

        ReadExcelGroup::readFile($path);
        //return response()->json(['success'=>'Form is successfully submitted!']);
    }
}
