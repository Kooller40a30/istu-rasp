@extends('layouts.app')

@section('title')
    Расписание аудиторий
@endsection

@section('content')
    <div class="text-center mt-3">
        <h2 class="mb-lg-3">Расписание аудиторий</h2>
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
                <a class="btn btn-secondary mb-lg-4" href="{{route('get_classrooms')}}" style="font-size: 18px">Сбросить</a>
            </div>
            <form method="post" action="{{route('classrooms_faculty')}}" id="faculties">
                @csrf
                <h5 class="mb-2">Выберите институт/факультет</h5>
                <div class="row">
                    <div class="col-6 -sm">
                        <select class="form-select mb-lg-3" name="faculty">
                            <option disabled selected>Выберите институт/факультет</option>
                            @foreach($faculties as $faculty)
                                @if($faculty_id != -1 && $faculty_id == $faculty['id'])
                                    <option selected value="{{$faculty['id']}}" >{{$faculty['nameFaculty']}}</option>
                                @else <option value="{{$faculty['id']}}" >{{$faculty['nameFaculty']}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col ">
                        <input type="submit" class="btn btn-primary mb-lg-4" name="departments" style="font-size: 18px" value="Найти кафедры и аудиториии"/>
                        <input type="submit" class="btn btn-primary mb-lg-4" name="download_faculty" style="font-size: 18px" value="Скачать"/>
                    </div>
                </div>
            </form>
            <form method="post" action="{{route('classrooms_department')}}" id="departments">
                @csrf
                <h5 class="mb-2">Выберите кафедру</h5>
                <div class="row">
                    <div class="col-6 -sm">
                        <select class="form-select mb-lg-3" name="department">
                            <option disabled selected>Выберите кафедру</option>
                            @foreach($departments as $department)
                                @if($department_id != -1 && $department_id == $department['id'])
                                    <option selected value="{{$department['id'] . '.' . $department['faculty_id']}}" >{{$department['nameDepartment']}}</option>
                                @else <option value="{{$department['id'] . '.' . $department['faculty_id']}}" >{{$department['nameDepartment']}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <input type="submit" class="btn btn-primary mb-lg-4" name="classrooms" style="font-size: 18px" value="Найти аудитории"/>
                        <input type="submit" class="btn btn-primary mb-lg-4" name="show_department" style="font-size: 18px" value="Показать"/>
                        <input type="submit" class="btn btn-primary mb-lg-4" name="download_department" style="font-size: 18px" value="Скачать"/>
                    </div>
                </div>
            </form>
            <form method="post" action="{{route('classrooms_classroom')}}" id="teachers">
                @csrf
                <h5 class="mb-2">Выберите аудиторию</h5>
                <div class="row">
                    <div class="col-6 -sm">
                        <select class="form-select mb-lg-3" name="classroom">
                            <option disabled selected>Выберите аудиторию</option>
                            @foreach($classrooms as $classroom)
                                @if($classroom_id != -1 && $classroom_id == $classroom['id'])
                                    <option selected value="{{$classroom['id'] . '.' . $classroom['faculty_id'] . '.' . $classroom['department_id']}}" >{{$classroom['numberClassroom']}}</option>
                                @else <option value="{{$classroom['id'] . '.' . $classroom['faculty_id'] . '.' . $classroom['department_id']}}" >{{$classroom['numberClassroom']}}</option>
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
    <div class="table-responsive " style="max-height: 600px; margin: 1rem;">
     <h4>{{$title}}</h4>
        <div class="table table-striped table-bordered">
            @php readfile($html)@endphp
        </div>
    </div>
@endsection