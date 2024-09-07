<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Services\FileService;

class UploadController extends Controller 
{
    public function uploadFiles(FileRequest $request)
    {
        set_time_limit(0);
        $files = $request->file('import_file');
        FileService::processFiles((int)$request->type, $files);
    }
}