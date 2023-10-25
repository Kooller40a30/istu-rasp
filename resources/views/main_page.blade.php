@extends('layouts.app')

@section('title')
Главная страница
@endsection

@section('content')

<main class="site-wrapper">
    <div class="container mt-2">
        <div class="row align-items-center">
            <div class="col-sm-3 mb-2"><a class="logo" href="https://istu.ru/" target="_blank"></a></div>
            <div class="col-sm-auto mb-2">
                <p class="name-university">ФЕДЕРАЛЬНОЕ ГОСУДАРСТВЕННОЕ БЮДЖЕТНОЕ ОБРАЗОВАТЕЛЬНОЕ УЧРЕЖДЕНИЕ ВЫСШЕГО
                    ОБРАЗОВАНИЯ</p>
                <p class="name-university">ИЖЕВСКИЙ ГОСУДАРСТВЕННЫЙ ТЕХНИЧЕСКИЙ УНИВЕРСИТЕТ ИМЕНИ М.Т. КАЛАШНИКОВА</p>
            </div>
        </div>
    </div>
    
    <div class="container mt-3">
        <div class="row row-auto">            
            
            <div class="col-sm-3">
                <div class="list-group mb-3">
                    <li class="list-group-item bg-light">Расписание</li>
                    <button class="list-group-item list-group-item-action" id="group-tab" data-bs-toggle="tab" data-bs-target="#group-tab-pane" type="button" role="tab" aria-controls="group-tab-pane">Для групп</button>
                    <button class="list-group-item list-group-item-action" id="teacher-tab" data-bs-toggle="tab" data-bs-target="#teacher-tab-pane" type="button" role="tab" aria-controls="teacher-tab-pane">Для преподавателей</button>
                    <button class="list-group-item list-group-item-action" id="room-tab" data-bs-toggle="tab" data-bs-target="#room-tab-pane" type="button" role="tab" aria-controls="room-tab-pane">Для аудиторий</button>
                </div>                
            </div>          

            <div class="col-sm-9 tab-content" id="form-content">
                @include('group_category')
                @include('teacher_category')
                @include('room_category')
            </div>

            <div class="col-12">
                @include('result_schedule')    
            </div>

        </div>
    </div>        
</main>
<footer></footer>
@endsection