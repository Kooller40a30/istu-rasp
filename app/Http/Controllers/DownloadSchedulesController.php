<?php

namespace App\Http\Controllers;

use App\ReadExcel\TemplateExcelReader;
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

    public function readSchedules(Request $request) 
    {        
        set_time_limit(0);
        $rules = array('import_files'  => 'required');

        $error = Validator::make($request->all(), $rules);

        if($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        // новый парсер (2023 год)
        $files = $request->file('import_files');
        $excelReader = new TemplateExcelReader();
        $excelReader->processFiles($files);
        
        // старый парсер (2022 год)
        // ReadExcelSchedule::readFiles($request->file('import_files'));

        $output = array('success' => ' ');
        return response()->json($output);
    }
}