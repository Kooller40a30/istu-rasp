<?php

namespace App\Services;

use App\Models\Discipline;

class DisciplineService
{
    public static function addDiscipline(string $discipline)
    {
        Discipline::firstOrCreate(['nameDiscipline' => $discipline]);
        return Discipline::where('nameDiscipline', $discipline);
    }
}