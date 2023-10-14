@extends('layouts.app')

@section('title')
    Расписание групп
@endsection

@section('content')
    <div class="text-center mt-3">
        <h2 class="mb-lg-3">Расписание групп</h2>
        @if($errors->any())
            <div class="alert alert-danger w-25 container" style="height: 55px;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div class=" w-100 p-2">
        <div>
            <a class="btn btn-secondary mb-lg-4" href="{{route('get_groups')}}" style="font-size: 18px">Сбросить</a>
        </div>
        <div class="w-75 p-0">
            <form method="post" action="{{route('groups_faculty')}}" id="faculties">
                @csrf
                <h5 class="mb-2">Выберите институт/факультет</h5>
                <div class="row">
                    <div class="col-6 -sm">
                        <select class="form-select mb-lg-3" name="faculty">
                            <option disabled selected>Выберите институт/факультет</option>
                            @foreach($faculties as $faculty)
                                @if($faculty_id != 0 && $faculty_id == $faculty['id'])
                                    <option selected value="{{$faculty['id']}}" >{{$faculty['nameFaculty']}}</option>
                                @else <option value="{{$faculty['id']}}" >{{$faculty['nameFaculty']}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col ">
                        <input type="submit" class="btn btn-primary mb-lg-4" name="courses" style="font-size: 18px" value="Найти курсы и группы"/>
                    </div>
                </div>
            </form>
            <form method="post" action="{{route('groups_course')}}" id="courses">
                @csrf
                <h5 class="mb-2">Выберите курс</h5>
                <div class="row">
                    <div class="col-6 -sm">
                        <select class="form-select mb-lg-3" name="course">
                            <option disabled selected>Выберите курс</option>
                            @if(isset($courses))
                                @foreach($courses as $course)
                                    @if($courseName != 0 && $courseName == $course['course'])
                                        <option selected value="{{$course['faculty_id'] . '.' . $course['course']}}" >{{$course['course']}}</option>
                                    @else <option value="{{$course['faculty_id'] . '.' . $course['course']}}" >{{$course['course']}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col">
                        <input type="submit" class="btn btn-primary mb-lg-4" name="groups" style="font-size: 18px" value="Найти группы"/>
                        <input type="submit" class="btn btn-primary mb-lg-4" name="show" style="font-size: 18px" value="Показать"/>
                    </div>
                </div>
            </form>
            <form method="post" action="{{route('groups_group')}}" id="teachers">
                @csrf
                <h5 class="mb-2">Выберите группу</h5>
                <div class="row">
                    <div class="col-6 -sm">
                        <select class="form-select mb-lg-3" name="group">
                            <option disabled selected>Выберите группу</option>
                            @foreach($groups as $group)
                                @if($group_id != 0 && $group_id == $group['id'])
                                    <option selected value="{{$group['id']}}" >{{$group['nameGroup']}}</option>
                                @else <option value="{{$group['id']}}" >{{$group['nameGroup']}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <input type="submit" class="btn btn-primary mb-lg-4" name="show" style="font-size: 18px" value="Показать"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
