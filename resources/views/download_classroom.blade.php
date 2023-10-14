@extends('layouts.app')

@section('title')
    Загрузка аудиторий
@endsection

@section('content')
<div class="container w-50">
    <form id="formFile" method="POST" action="{{ url('/download_classrooms') }}" enctype="multipart/form-data">
        @csrf
        <div class="d-grid gap-1 w-25 p-3 position-absolute top-50 start-50 translate-middle">
            <label class="mb-lg-5 text-center fs-3 fw-bold" for="ExcelFile">Загрузите аудитории</label>
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
            <input class="form-control bg-secondary-subtle mb-lg-4" name="import_file" type="file" id="ExcelFile" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/>
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
            $('#formFile').ajaxForm({
                beforeSend: function () {
                    var percentage = '0';
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    var percentage = percentComplete;
                    $('.progress .progress-bar').css("width", percentage+'%', function() {
                        return $(this).attr("aria-valuenow", percentage) + "%";
                    })
                },
                complete: function (xhr) {
                    console.log('File has uploaded');
                    mess.html('<div class = "alert alert-success">Аудиториии загружены</div>');
                    $('.progress .progress-bar').css("width", 0+'%', function() {
                        return $(this).attr("aria-valuenow", 0) + "%";
                    })
                }
            });
        });
    });
</script>
@endsection

