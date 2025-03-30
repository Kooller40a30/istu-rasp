<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function makeScheduleHeader(array $lines): string
    {
        $filters = implode('<br>', $lines);

        $legend = <<<HTML
        <div class="d-flex flex-wrap align-items-center gap-3 mt-2">
            <strong>Виды занятий:</strong>
            <label><input type="color" id="color-lecture" value="#fff3e0"> Лекция (Л)</label>
            <label><input type="color" id="color-practice" value="#e8f5e9"> Практика (П)</label>
            <label><input type="color" id="color-lab" value="#e0f7fa"> Лабораторная (ЛБ)</label>
        </div>
        HTML;

        return $filters . '<br>' . $legend;
    }
}