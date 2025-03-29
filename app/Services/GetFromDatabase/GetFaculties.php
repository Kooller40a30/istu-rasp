<?php

namespace App\Services\GetFromDatabase;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Collection;

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
        return Faculty::where('shortNameFaculty', '!=', 'Институт физической культуры и спорта')
            ->where('shortNameFaculty', '!=', 'НОЦ ПиМНКДиС')
            ->where('shortNameFaculty', '!=', 'УКО')
            ->orderBy('nameFaculty')
            ->get();
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
        $faculties = Faculty::where('shortNameFaculty', '!=', 'Институт физической культуры и спорта')
            ->where('shortNameFaculty', '!=', 'НОЦ ПиМНКДиС')
            ->where('shortNameFaculty', '!=', 'УКО')
            ->orderBy('nameFaculty')
            ->get();

        $faculties->push((object)[
            'id'          => 0,
            'nameFaculty' => 'без института/факультета'
        ]);

        return $faculties;
    }

    /**
     * Получает факультеты, связанные с группами.
     *
     * @return Collection|Faculty[]
     */
    public static function findFacultiesForGroups(): Collection
    {
        return Faculty::select('faculties.id', 'nameFaculty')
            ->join('groups', 'faculties.id', '=', 'groups.faculty_id')
            ->groupBy('nameFaculty', 'faculties.id')
            ->get();
    }
}
