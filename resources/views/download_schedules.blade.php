@extends('layouts.app')

@section('title')
Загрузка группового расписания
@endsection

@section('content')
    <div class="container w-50">
        <form id="formFile" method="POST" action="{{ url('/download_schedules') }}" enctype="multipart/form-data">
            @csrf
            <div class="d-grid gap-1 w-25 p-3 position-absolute top-50 start-50 translate-middle">
                <label class="mb-lg-3 text-center fs-3 fw-bold" for="ExcelFile">Загрузите групповое расписание</label>
                @if($error != '')
                    <div class="alert alert-danger container" style="height: 55px;">
                        <ul>
                            {{$error}}
                        </ul>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger container" style="height: 55px;">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{$error}}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="mess"> </div>
                <input class="form-control bg-secondary-subtle mb-lg-4" name="import_files[]" type="file" id="ExcelFile" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" multiple/>
                <input type="submit" class="btn btn-primary mb-lg-4" style="font-size: 20px" value="Загрузить"/>
                <div class="form-group">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated " role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                    </div>
                </div>
                <div class="pt-5">
                    <a class="w-100 btn btn-lg btn-secondary" href="/">На главную</a>
                </div>
            </div>
        </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
    <script>
        $(function () {
            $(document).ready(function () {
                let mess = $('.mess');
                mess.html('<div></div>');
                $('#formFile').ajaxForm({
                    beforeSend: function () {
                        $('#success').empty();
                    },
                    uploadProgress: function (event, position, total, percentComplete) {
                        $('.progress-bar').text('Идет загрузка');
                        $('.progress-bar').css('width', percentComplete + '%');
                        mess.html('<div class = "alert alert-primary text-center">Подождите</div>');
                    },
                    success: function (data) {
                        if (data.success) {
                            $('.progress-bar').text('Загружено');
                            $('.progress-bar').css('width', '0%');
                            //$('#success').append(data.image);
                            mess.html('<div class = "alert alert-success text-center">Расписание загружено</div>');
                        }
                        /*else {
                            $('.progress-bar').text('0%');
                            $('.progress-bar').css('width', '0%');
                            mess.html('<div class = "alert alert-danger text-center">Файлы с расписанием не найдены</div>');
                        }*/
                    },
                });
            });
        });
    </script>
@endsection

