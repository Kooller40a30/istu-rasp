<?php

namespace App\ReadExcel;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GetPath
{
    public static function savePath($file)
    {
        $filename = $file->getClientOriginalName();
        $upload_folder = 'public/excel';
        Storage::putFileAs($upload_folder, $file, $filename);
        $filePath = 'app//public//excel//' . $filename;
        return storage_path($filePath);
    }
}