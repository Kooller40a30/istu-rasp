<?php

namespace App\Services\GetFromDatabase;

use App\Models\Faculty;

class GetFaculties{
    public static function facultiesToTeachers(){
        return Faculty::where('shortNameFaculty','!=','Институт физической культуры и спорта')
            ->where('shortNameFaculty','!=','НОЦ ПиМНКДиС')
            ->where('shortNameFaculty','!=','УКО')
            ->orderBy('nameFaculty')->get();
    }

    public static function facultiesToClassrooms(){
        $faculties = Faculty::where('shortNameFaculty','!=','Институт физической культуры и спорта')
            ->where('shortNameFaculty','!=','НОЦ ПиМНКДиС')
            ->where('shortNameFaculty','!=','УКО')
            ->orderBy('nameFaculty')->get();
        $countFaculties = count($faculties);
        $faculties[$countFaculties] = [
            'id' => 0,
            'nameFaculty' => 'без института/факультета'
        ];
        return $faculties;
    }
    public static function facultiesToGroups(){
        return Faculty::select('faculties.id','nameFaculty')
            ->join('groups','faculties.id','=','groups.faculty_id')
            ->groupBy('nameFaculty','faculties.id')
            ->get();
    }
}
