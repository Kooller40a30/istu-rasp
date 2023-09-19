@extends('layouts.app')

@section('title')
    Главная страница
@endsection

@section('content')
    <div class="container">
    <div class="pricing-header p-4 m-4 pb-md-4 mx-auto text-center">
        <h2 class="display-4 fw-normal text-black">Расписание групп, преподавателей, аудиторий</h2>
    </div>
        <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
            <div class="col" style="height: 600px;">
                <div class="card mb-4 rounded-3 shadow-sm" >
                    <div class="card-header py-3">
                        <h4 class="my-0 fw-normal">Расписание групп</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li class="fst-italic">Расписание групп</li>
                            <li class="fst-italic">по</li>
                            <li class="fst-italic">факультетам/институтам</li>
                            <li class="fst-italic">и</li>
                            <li class="fst-italic">курсам</li>
                            <li class="text-white">и</li>
                        </ul>
                        <a class="w-100 btn btn-lg btn-primary" href="{{route('get_groups')}}">Перейти</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card mb-4 rounded-3 shadow-sm">
                    <div class="card-header py-3">
                        <h4 class="my-0 fw-normal">Расписание преподавателей</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li class="fst-italic">Расписание преподавателей вуза</li>
                            <li class="fst-italic">по</li>
                            <li class="fst-italic">факультетам/институтам,</li>
                            <li class="fst-italic">кафедрам</li>
                            <li class="fst-italic">и фамилиям</li>
                            <li class="text-white">и</li>
                        </ul>
                        <a class="w-100 btn btn-lg btn-primary" href="{{route('get_teachers')}}">Перейти</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card mb-4 rounded-3 shadow-sm">
                    <div class="card-header py-3">
                        <h4 class="my-0 fw-normal">Расписание аудиторий</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li class="fst-italic">Расписание аудиторий</li>
                            <li class="fst-italic">по</li>
                            <li class="fst-italic">факультетам/институтам,</li>
                            <li class="fst-italic">кафедрам</li>
                            <li class="fst-italic">и номерам аудиторий</li>
                            <li class="text-white">и</li>
                        </ul>
                        <a class="w-100 btn btn-lg btn-primary" href="{{route('get_classrooms')}}">Перейти</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection