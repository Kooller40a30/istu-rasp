<?php

namespace App\Http\Controllers;

use App\Models\Error;

class ErrorController extends Controller 
{
    public function renderLogs()
    {
        $title = 'Журнал логов';
        $errors = Error::all();
        $errAttrs = Error::getAttributeLabels();
        $data = compact('errors', 'errAttrs', 'title');
        return view('logs', $data);
    }
}