<?php

namespace App\Services;

use App\Models\Discipline;
use Illuminate\Support\Facades\Log;

class DisciplineService
{
    public static function addDiscipline(string $discipline): Discipline
    {
        Log::info('[DisciplineService] Action: Attempting to add or find discipline', ['discipline' => $discipline]);
        $disciplineRecord = Discipline::firstOrCreate(['nameDiscipline' => $discipline]);
        Log::info('[DisciplineService] Action: Discipline record found or created', ['discipline_id' => $disciplineRecord->id, 'discipline_name' => $disciplineRecord->nameDiscipline]);
        return $disciplineRecord;
    }
}