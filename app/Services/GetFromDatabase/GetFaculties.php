<?php

namespace App\Services\GetFromDatabase;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Репозиторий для работы с факультетами.
 */
class GetFaculties
{
    /**
     * Получает факультеты для преподавателей.
     *
     * Исключает факультеты с короткими именами:
     * - Институт физической культуры и спорта
     * - НОЦ ПиМНКДиС
     * - УКО
     *
     * @return Collection|Faculty[]
     */
    public static function findFacultiesForTeachers(): Collection
    {
        Log::info('[GetFaculties] Action: Finding faculties for teachers');
        $result = Faculty::where('shortNameFaculty', '!=', 'Институт физической культуры и спорта')
            ->where('shortNameFaculty', '!=', 'НОЦ ПиМНКДиС')
            ->where('shortNameFaculty', '!=', 'УКО')
            ->orderBy('nameFaculty')
            ->get();
        Log::info('[GetFaculties] Action: Found faculties for teachers', ['count' => $result->count()]);
        return $result;
    }

    /**
     * Получает факультеты для аудиторий с добавлением элемента "без института/факультета".
     *
     * Исключает факультеты с короткими именами:
     * - Институт физической культуры и спорта
     * - НОЦ ПиМНКДиС
     * - УКО
     *
     * @return Collection|Faculty[]
     */
    public static function findFacultiesForClassrooms(): Collection
    {
        Log::info('[GetFaculties] Action: Finding faculties for classrooms');
        $faculties = Faculty::where('shortNameFaculty', '!=', 'Институт физической культуры и спорта')
            ->where('shortNameFaculty', '!=', 'НОЦ ПиМНКДиС')
            ->where('shortNameFaculty', '!=', 'УКО')
            ->orderBy('nameFaculty')
            ->get();

        $faculties->push((object)[
            'id'          => 0,
            'nameFaculty' => 'без института/факультета'
        ]);

        Log::info('[GetFaculties] Action: Found faculties for classrooms', ['count' => $faculties->count()]);
        return $faculties;
    }

    /**
     * Получает факультеты, связанные с группами.
     *
     * @return Collection|Faculty[]
     */
    public static function findFacultiesForGroups(): Collection
    {
        Log::info('[GetFaculties] Action: Finding faculties for groups');
        $result = Faculty::select('faculties.id', 'nameFaculty')
            ->join('groups', 'faculties.id', '=', 'groups.faculty_id')
            ->groupBy('nameFaculty', 'faculties.id')
            ->get();
        Log::info('[GetFaculties] Action: Found faculties for groups', ['count' => $result->count()]);
        return $result;
    }
}
