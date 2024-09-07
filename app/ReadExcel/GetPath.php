<?php

namespace App\ReadExcel;

use Illuminate\Support\Facades\Storage;

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