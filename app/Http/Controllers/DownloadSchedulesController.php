<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Error;
use App\Models\Schedule;
use App\Models\Teacher;
use App\ReadExcel\GetPath;
use App\ReadExcel\ReadExcelClassroom;
use App\ReadExcel\ReadExcelSchedule;
use App\ReadExcel\ReadExcelTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DownloadSchedulesController extends Controller
{
    public function index(){

        $files = collect(Storage::allFiles('public//excel'));
        $files->each(function ($file) {
            $lastModified =  Storage::lastModified($file);
            $lastModified = Carbon::parse($lastModified);

            if (Carbon::now()->gt($lastModified->addHour(12))) {
                Storage::delete($file);
            }
        });

        $error = '';
        return view('download_schedules',compact('error'));
    }

    public function readSchedules(Request $request){
        //dd($request);
        set_time_limit(0);
        $rules = array(
            'import_files'  => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            //dd($error->errors()->all());
            return response()->json(['errors' => $error->errors()->all()]);
        }
        else {
            ReadExcelSchedule::readFiles($request->file('import_files'));
            /*Schedule::where('id', '>', 0)->delete();
            Error::where('id', '>', 0)->delete();
            foreach ($request->file('import_files') as $file){
                $filename = $file->getClientOriginalName();
                $path = GetPath::savePath($file);
                ReadExcelSchedule::readFile($path, $filename);
            }*/
            $output = array(
                'success' => ' ',
            );
            return response()->json($output);
        }
        //return response()->json(['success'=>'Form is successfully submitted!']);
    }
}