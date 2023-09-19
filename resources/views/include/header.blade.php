@section('header')
<header>
    <nav class="navbar navbar-expand-sm navbar-primary bg-primary" >
        <div class="container-fluid">
            <a class="navbar-brand text-white fw-bold fst-italic"  href="/">Расписание ИжГТУ имени М. Т. Калашникова</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-sm-0 ms-4">
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="/">Главная</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active text-white" href="#" data-bs-toggle="dropdown" aria-expanded="false">Расписание</a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown03">
                            <li><a class="dropdown-item" href="{{route('get_groups')}}">Групп</a></li>
                            <li><a class="dropdown-item" href="{{route('get_teachers')}}">Преподавателей</a></li>
                            <li><a class="dropdown-item" href="{{route('get_classrooms')}}">Аудиторий</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="{{route('errors')}}">Скачать ошибки</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active text-white" href="#" data-bs-toggle="dropdown" aria-expanded="false">Редактирование</a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown03">
                            <li><a class="dropdown-item" href="{{route('download_faculties_page')}}">Загрузить институты/факультеты</a></li>
                            <li><a class="dropdown-item" href="{{route('download_departments_page')}}">Загрузить кафедры</a></li>
                            <li><a class="dropdown-item" href="{{route('download_teachers_page')}}">Загрузить преподавателей</a></li>
                            <li><a class="dropdown-item" href="{{route('download_classrooms_page')}}">Загрузить аудитории</a></li>
                            <li><a class="dropdown-item" href="{{route('download_groups_page')}}">Загрузить группы</a></li>
                            <li><a class="dropdown-item" href="{{route('download_schedules_page')}}">Загрузить групповое расписание</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>