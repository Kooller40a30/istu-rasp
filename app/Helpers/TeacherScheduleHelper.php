<?php

namespace App\Helpers;

use App\Models\ClassModel;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Teacher;
use App\Services\GetFromDatabase\ScheduleRepository;

class TeacherScheduleHelper extends ScheduleHelper
{
    
    public static function generateTeachersSchedule(Faculty $faculty, Department $department = null) : string
    {
        $html = "";
        
        return $html;
    }


}