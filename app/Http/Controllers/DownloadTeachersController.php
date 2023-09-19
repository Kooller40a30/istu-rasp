<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Teacher;
use App\ReadExcel\GetPath;
use App\ReadExcel\ReadExcelClassroom;
use App\ReadExcel\ReadExcelTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadTeachersController extends Controller
{
    public function index(){
        return view('download_teacher');
    }

    public function readTeacher(FileRequest $request){
        set_time_limit(0);
        $file = $request->file('import_file');

        $path = GetPath::savePath($file);
        /*$filename = $file->getClientOriginalName();
        $upload_folder = 'public_html/excel';
        Storage::putFileAs($upload_folder, $file, $filename);
        $filePath = '//app//public_html//excel//' . $filename;
        $path = storage_path($filePath);*/

        ReadExcelTeacher::readFile($path);
        //return response()->json(['success'=>'Form is successfully submitted!']);
    }
}
