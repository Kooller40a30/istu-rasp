@section('header')
<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container justify-content-end">
            @if (Auth::check())
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#uploadModal">Загрузить файлы</button></li>
                        <li><a class="dropdown-item" target="_blank" href="{{ route('renderLogs') }}">Просмотр логов</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}">Выход</a></li>
                    </ul>
                </li>
            </ul>
            @else
            <button id="showLoginFormButton" class="nav-link link-animation" data-bs-toggle="modal" data-bs-target="#loginModal">Для администратора</button>
            @endif
        </div>
    </nav>
</header>
@show