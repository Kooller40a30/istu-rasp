@extends('layouts.app')

@section('title')
    Расписание преподавателей по кафедрам
@endsection

@section('content')
    <div class="text-center mt-3">
        <h2 class="mb-lg-3">Расписание преподавателей</h2>
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
            <div class="w-75 p-0">
                <div>
                    <a class="btn btn-secondary mb-lg-4" href="{{route('get_teachers')}}" style="font-size: 18px">Сбросить</a>
                </div>
                <form method="post" action="{{route('teachers_faculty')}}" id="faculties">
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
                            <input type="submit" class="btn btn-primary mb-lg-4" name="departments" style="font-size: 18px" value="Найти кафедры и преподавателей"/>
                            <input type="submit" class="btn btn-primary mb-lg-4" name="download" style="font-size: 18px" value="Скачать"/>
                        </div>
                    </div>
                </form>
                <form method="post" action="{{route('teachers_department')}}" id="departments">
                    @csrf
                    <h5 class="mb-2">Выберите кафедру</h5>
                    <div class="row">
                        <div class="col-6 -sm">
                            <select class="form-select mb-lg-3" name="department">
                                <option disabled selected>Выберите кафедру</option>
                                @foreach($departments as $department)
                                    @if($department_id != 0 && $department_id == $department['id'])
                                        <option selected value="{{$department['id']}}" >{{$department['nameDepartment']}}</option>
                                    @else <option value="{{$department['id']}}" >{{$department['nameDepartment']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <input type="submit" class="btn btn-primary mb-lg-4" name="teachers" style="font-size: 18px" value="Найти преподавателей"/>
                            <input type="submit" class="btn btn-primary mb-lg-4" name="show" style="font-size: 18px" value="Показать"/>
                            <input type="submit" class="btn btn-primary mb-lg-4" name="download" style="font-size: 18px" value="Скачать"/>
                        </div>
                    </div>
                </form>
                <form method="post" action="{{route('teachers_teacher')}}" id="teachers">
                    @csrf
                    <h5 class="mb-2">Выберите преподавателя</h5>
                    <div class="row">
                        <div class="col-6 -sm">
                            <select class="form-select mb-lg-3" name="teacher">
                                <option disabled selected>Выберите преподавателя</option>
                                @foreach($teachers as $teacher)
                                    @if($teacher_id != 0 && $teacher_id == $teacher['id'])
                                        <option selected value="{{$teacher['id']}}" >{{$teacher['nameTeacher']}}</option>
                                    @else <option value="{{$teacher['id']}}" >{{$teacher['nameTeacher']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <input type="submit" class="btn btn-primary mb-lg-4" name="show" style="font-size: 18px" value="Показать"/>
                            <input type="submit" class="btn btn-primary mb-lg-4" name="download" style="font-size: 18px" value="Скачать"/>
                        </div>
                    </div>
                </form>
            </div>
    </div>

@endsection
