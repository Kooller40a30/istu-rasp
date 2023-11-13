<?php

use App\Http\Controllers\ClassroomsController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\DownloadClassroomsController;
use App\Http\Controllers\DownloadDepartmentsController;
use App\Http\Controllers\DownloadFacultiesController;
use App\Http\Controllers\DownloadGroupsController;
use App\Http\Controllers\DownloadSchedulesController;
use App\Http\Controllers\DownloadTeachersController;
use App\Http\Controllers\ErrorsController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\StartController;
use App\Http\Controllers\TeachersController;
use App\Services\GetFromDatabase\GetGroups;
use Illuminate\Support\Facades\Route;
use Psy\Util\Json;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/*Route::get('/create_excel', [ExampleCreateExcelController::class, 'index'])->name('create_excel');
Route::get('/create_excel_classrooms', [ExampleCreateExcelController::class, 'createClassroomsExcel'])->name('create_excel_classrooms');
Route::get('/get_file_teacher', [ExampleCreateExcelController::class, 'getFileTeacher'])->name('get_file_teacher');
Route::get('/get_file_classroom', [ExampleCreateExcelController::class, 'getFileClassroom'])->name('get_file_classroom');

Route::post('/download_file', [ExampleCreateExcelController::class, 'downloadFile'])->name('download_file');



Route::get('/index', [ExampleFileController::class, 'read'])->name('index');
Route::get('/read', [ExampleFileController::class, 'read'])->name('read');
Route::get('/delete', [ExampleFileController::class, 'delete'])->name('delete');
Route::get('/create', [ExampleFileController::class, 'create'])->name('create');
Route::get('/read_file', [ExampleFileController::class, 'readFile'])->name('rd');

Route::post('/read_excel_file', [ExampleFileExcelController::class, 'readFile'])->name('r_e_d');
Route::post('/read_excel_files', [ExampleFileExcelController::class, 'readFiles'])->name('r_e_d_2');
Route::get('/get_exception_group', [ExampleFileController::class, 'getExceptionGroup']);


Route::get('/get_building', [ClassroomsController::class, 'getBuilding'])->name('get_building');
Route::get('/get_department', [TeachersController::class, 'getDepartments'])->name('get_department');

//Route::get('/', [StartController::class, 'index'])->name('start');
//Route::post('/read_files', [ReadFilesExcelController::class, 'readFiles'])->name('read_files');

Route::post('/create_teacher', [CreateTableController::class, 'createTeacher'])->name('create_teacher');
Route::post('/create_classroom', [CreateTableController::class, 'createClassroom'])->name('create_classroom');

Route::get('/get_teacher', [ExampleTeacherController::class, 'getTeacher'])->name('get_teacher');
Route::get('/get_classroom', [ExampleClassroomController::class, 'getClassroom'])->name('get_classroom');

//Route::get('/get_classrooms', [ClassroomsController::class, 'getClassrooms'])->name('get_classrooms');
//Route::get('/get_teachers', [TeachersController::class, 'getTeachers'])->name('get_teachers');

Route::post('/get_excel_classrooms', [ExampleDownloadsExcelController::class, 'downloadExcelClassrooms'])->name('get_excel_classrooms');
Route::post('/get_excel_teachers', [ExampleDownloadsExcelController::class, 'downloadExcelTeachers'])->name('get_excel_teachers');

Route::get('/choose', [ExampleChooseController::class, 'index'])->name('choose');

Route::get('/teacher_schedule', function () {
    return view('teacher_schedule');
});

Route::get('/classrooms_schedule', function () {
    return view('classrooms_schedule');
});

//загрузка файлов
Route::post('/read_faculty', [ReadFilesExcelController::class, 'readFaculty'])->name('read_faculty');
Route::post('/read_department', [ReadFilesExcelController::class, 'readDepartment'])->name('read_department');
Route::post('/read_group', [ReadFilesExcelController::class, 'readGroup'])->name('read_group');
Route::post('/read_teacher', [ReadFilesExcelController::class, 'readTeacher'])->name('read_teacher');
Route::post('/read_classroom', [ReadFilesExcelController::class, 'readClassroom'])->name('read_classroom');
Route::post('/read_schedule', [ReadFilesExcelController::class, 'readSchedules'])->name('read_schedule');

Route::get('/faculty', function () {
    return view('download_faculty');
});

Route::get('/department', function () {
    return view('download_department');
});

Route::get('/group', function () {
    return view('download_group');
});

Route::get('/teacher', function () {
    return view('download_teacher');
});

Route::get('/classroom', function () {
    return view('download_classroom');
});

//Route::get('/classroom_schedule', function () {
//    return view('classroom_schedule');
//});

//Ошибки
Route::get('/create_errors', [ExcelErrors::class, 'createErrors'])->name('create_errors');
Route::get('/table_errors', [CreateTableController::class, 'createTable'])->name('table_errors');
//проверка одного преподавателя  и аудитории
Route::get('/table_teacher', [CreateTableController::class, 'createTableTeacher'])->name('table_teacher');
Route::get('/table_classroom', [CreateTableController::class, 'createTableClassroom'])->name('table_classroom');
Route::get('/table_teachers', [CreateTableController::class, 'createTableTeachers'])->name('table_teachers');
Route::get('/table_classrooms', [CreateTableController::class, 'createTableClassrooms'])->name('table_classrooms');
Route::get('/table_faculty_teachers', [CreateTableController::class, 'createFacultyTeachers'])->name('table_faculty_teachers');
Route::get('/table_faculty_classrooms', [CreateTableController::class, 'createFacultyClassrooms'])->name('table_faculty_classrooms');*/

//начало
//главная страница
Route::get('/', [StartController::class, 'index'])->name('start');

//ошибки
Route::get('/errors', [ErrorsController::class, 'index'])->name('errors');

//расписание преподавателей
Route::get('/teachers', [TeachersController::class, 'getAllTeachers'])->name('get_teachers');
Route::post('/teachers_faculty', [TeachersController::class, 'getFacultyTeachers'])->name('teachers_faculty');
Route::post('/teachers_department', [TeachersController::class, 'getDepartmentTeachers'])->name('teachers_department');
Route::post('/teachers_teacher', [TeachersController::class, 'getTeacher'])->name('teachers_teacher');

//расписание аудиторий
Route::get('/classrooms', [ClassroomsController::class, 'getAllClassrooms'])->name('get_classrooms');
Route::post('/classrooms_faculty', [ClassroomsController::class, 'getFacultyClassrooms'])->name('classrooms_faculty');
Route::post('/classrooms_department', [ClassroomsController::class, 'getDepartmentClassrooms'])->name('classrooms_department');
Route::post('/classrooms_classroom', [ClassroomsController::class, 'getClassroom'])->name('classrooms_classroom');

//расписание групп
Route::get('/groups', [GroupsController::class, 'getAllGroups'])->name('get_groups');
Route::post('/groups_faculty', [GroupsController::class, 'getFacultyGroups'])->name('groups_faculty');
Route::post('/groups_course', [GroupsController::class, 'getCourse'])->name('groups_course');
Route::post('/groups_group', [GroupsController::class, 'getGroup'])->name('groups_group');
Route::get('/groups/{faculty?}/{course?}', [GroupsController::class, 'getGroups'])->name('getGroups');

// курс обучения
Route::get('/courses/{faculty?}', [CoursesController::class, 'getCourses'])->name('getCourses');

//загрузка файлов
//аудитории
Route::get('/download_classrooms_page', [DownloadClassroomsController::class, 'index'])->name('download_classrooms_page');
Route::post('/download_classrooms', [DownloadClassroomsController::class, 'readClassroom'])->name('download_classrooms');
//преподаватели
Route::get('/download_teachers_page', [DownloadTeachersController::class, 'index'])->name('download_teachers_page');
Route::post('/download_teachers', [DownloadTeachersController::class, 'readTeacher'])->name('download_teachers');
//групповое расписание
Route::get('/download_schedules_page', [DownloadSchedulesController::class, 'index'])->name('download_schedules_page');
Route::post('/download_schedules', [DownloadSchedulesController::class, 'readSchedules'])->name('download_schedules');
//группы
Route::get('/download_groups_page', [DownloadGroupsController::class, 'index'])->name('download_groups_page');
Route::post('/download_groups', [DownloadGroupsController::class, 'readGroups'])->name('download_groups');
//факультеты
Route::get('/download_faculties_page', [DownloadFacultiesController::class, 'index'])->name('download_faculties_page');
Route::post('/download_faculties', [DownloadFacultiesController::class, 'readFaculty'])->name('download_faculties');
//кафедры
Route::get('/download_departments_page', [DownloadDepartmentsController::class, 'index'])->name('download_departments_page');
Route::post('/download_departments', [DownloadDepartmentsController::class, 'readDepartment'])->name('download_departments');


