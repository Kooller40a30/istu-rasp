<?php

namespace App\Http\Controllers;

use App\CreateExcel\CreateGroups;
use App\Helpers\GroupScheduleHelper;
use App\Helpers\ScheduleHelper;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\FacultyRequest;
use App\Http\Requests\GroupRequest;
use App\Models\Faculty;
use App\Models\Group;
use App\Services\GetFromDatabase\GetCourses;
use App\Services\GetFromDatabase\GetFaculties;
use App\Services\GetFromDatabase\GetGroups;
use App\Services\GetFromDatabase\GetGroupsCourses;
use Illuminate\Http\Request;
use Psy\Util\Json;

class GroupsController extends Controller
{

    public function getAllGroups()
    {
        //Storage::deleteDirectory('public/schedule');
        //Storage::createDirectory('public/schedule');
        $faculties = GetFaculties::findFacultiesForGroups();        
        $courses = null;
        $groups = Group::all();
        $faculty_id = 0;
        $group_id = 0;
        $courseName = 0;
        return view('groups_schedule', compact('faculties', 'courses', 'groups', 'faculty_id', 'group_id', 'courseName'));
    }

    public function getCourse(CourseRequest $request)
    {
        set_time_limit(0);
        $course = explode('.', $_POST['course']);
        $faculty_id = $course[0];
        $courseName = $course[1];
        $group_id = 0;
        $faculties = GetFaculties::findFacultiesForGroups();
        $groups = GetGroups::findGroups($faculty_id, $courseName);
        $courses = GetGroupsCourses::findCoursesByFaculty($faculty_id);
        if (isset($_POST['groups'])) {
            return response()->view('groups_schedule', compact('faculties', 'courses', 'groups', 'faculty_id', 'group_id', 'courseName'));
        } elseif (isset($_POST['show'])) {
            $path = CreateGroups::createGroups($courseName, $faculty_id);
            $html = $path;
            $facultyName = Faculty::where('id', $faculty_id)->value('shortNameFaculty');
            $title = 'Расписание курса ' . $courseName . ' ' . $facultyName;
            return response()->view('groups_schedule_table', compact('faculties', 'courses', 'groups', 'faculty_id', 'group_id', 'courseName', 'html', 'title'));
        }
    }
    public function getGroup(GroupRequest $request)
    {
        set_time_limit(0);
        $group_id = $request['group'];
        if (isset($_POST['show'])) {
            $group = Group::where('id', $group_id)->get();
            $faculty_id = $group[0]['faculty_id'];
            $faculties = GetFaculties::findFacultiesForGroups();
            $courseName = $group[0]['course'];
            $courses = GetGroupsCourses::findCoursesByFaculty($faculty_id);
            $groups = GetGroups::findGroups($faculty_id, $courseName);
            $path = CreateGroups::createGroup($group_id);
            $html = $path;
            $groupName = $group[0]['nameGroup'];
            $title = 'Расписание группы ' . $groupName;
            return response()->view('groups_schedule_table', compact('faculties', 'courses', 'groups', 'faculty_id', 'group_id', 'courseName', 'html', 'title'));
        }
    }

    public function getGroups(Request $request)
    {
        $faculty_id = (int)$request->query('faculty');
        $course = (int)$request->query('course', 0);        
        $groups = GetGroups::findGroups($faculty_id, $course);
        $html = '<option selected="" value="">Все группы</option>';
        foreach ($groups as $group) {
            $id = $group['id'];
            $name = $group['nameGroup'];
            $html .= "<option value=\"$id\">$name</option>";
        }
        return response($html);
    }
    
    public function loadGroupSchedule(Request $request) 
    {
        $facultyId = (int) $request->query('faculty', 0);
        $courseId  = (int) $request->query('course', 0);
        $groupId   = (int) $request->query('group', 0);
    
        $faculty = $facultyId ? Faculty::find($facultyId) : null;
        $course  = $courseId  ? GetCourses::findById($courseId) : null;
        $group   = $groupId   ? Group::find($groupId) : null;
    
        $facultyName = $faculty?->shortNameFaculty ?? 'Все институты';
        $courseName  = $course?->nameCourse        ?? 'Все курсы';
        $groupName   = $group?->nameGroup          ?? 'Все группы';
    
        $header = "Институт: {$facultyName}<br>Курс: {$courseName}<br>Группа: {$groupName}";
    
        if ($group) {
            $result = GroupScheduleHelper::generateSchedule($group->schedules(), $groupName);
        } else {
            $result = GroupScheduleHelper::generateCourseSchedule($faculty, $course);
        }
    
        return response()->view('result_schedule', compact('result', 'header'));
    }
    
}