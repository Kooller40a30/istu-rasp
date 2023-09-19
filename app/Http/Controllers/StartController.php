<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class StartController extends Controller
{
    public function index(){

Storage::deleteDirectory('public_html/schedule');
        $files = collect(Storage::allFiles('public//schedule'));
        $files->each(function ($file) {
            $lastModified =  Storage::lastModified($file);
            $lastModified = Carbon::parse($lastModified);

            if (Carbon::now()->gt($lastModified->addHour(12))) {
                Storage::delete($file);
            }
        });

        return view('main_page');
    }
}