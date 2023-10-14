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
                <div class="card small-card mb-3">
                    <ul class="list-group list-group-flush" id="myTab" role="tablist">
                        <li class="list-group-item bg-light" role="presentation">Расписание</li>
                        <li class="list-group-item" role="presentation">
                            <button class="nav-link commands" id="group-tab" data-bs-toggle="tab"
                                data-bs-target="#group-tab-pane" type="button" role="tab"
                                aria-controls="group-tab-pane">Для групп</button>
                        </li>
                        <li class="list-group-item" role="presentation">
                            <button class="nav-link commands" id="teacher-tab" data-bs-toggle="tab"
                                data-bs-target="#teacher-tab-pane" type="button" role="tab"
                                aria-controls="teacher-tab-pane">Для преподавателей</button>
                        </li>
                        <li class="list-group-item" role="presentation">
                            <button class="nav-link commands" id="room-tab" data-bs-toggle="tab"
                                data-bs-target="#room-tab-pane" type="button" role="tab"
                                aria-controls="room-tab-pane">Для аудиторий</button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-9 tab-content" id="form-content">
                @include('group_category')
                @include('teacher_category')
                @include('room_category')
            </div>

        </div>
    </div>

    <div class="container mt-3">
        <div style="display: none;" id="result-content">
            <div class="card tab-panel">
                <input type="submit" class="btn btn-primary" name="download" value="Скачать">
                <div class="row row-auto">
                    <table class="table mt-2">
                        <thead>
                            <th scope="col">В разработке</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

</main>
<footer></footer>
<script>
    $(function () {
        $('input[type="submit"]').on('click', (event) => {
            $('#result-content').show();
            event.preventDefault();
        });
    });
</script>
@endsection