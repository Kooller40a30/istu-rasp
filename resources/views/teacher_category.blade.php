@section('teacher_category') 
<div class="card tab-pane fade" id="teacher-tab-pane" role="tabpanel" aria-labelledby="teacher-tab" tabindex="-1">
    <h5 class="card-header">Расписание для преподавателей</h5>
    <div class="card-body">
        <form method="post" action="http://istu-rasp/teachers_faculty" id="faculties">
            @csrf
            <div class="row">
                <label for="teacher" class="form-label">Выберите институт/факультет</label>
                <select class="form-select mb-3" name="faculty" id="teacher_faculty">
                    <option disabled="" selected="">Выберите институт/факультет
                    </option>
                    @foreach($faculties as $faculty)
                        @if($faculty_id != 0 && $faculty_id == $faculty['id'])
                            <option selected value="{{$faculty['id']}}" >{{$faculty['nameFaculty']}}</option>
                        @else <option value="{{$faculty['id']}}" >{{$faculty['nameFaculty']}}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="row">
                <label for="department" class="form-label">Выберите кафедру</label>
                <select class="form-select mb-3" name="department" id="department">
                    <option disabled="" selected="">Выберите кафедру</option>
                </select>
            </div>
            <div class="row">
                <label for="teacher" class="form-label">Выберите преподавателя</label>
                <select class="form-select mb-3" name="teacher" id="teacher">
                    <option disabled="" selected="">Выберите преподавателя</option>
            </div>

            <div class="row">
                <input type="submit" class="btn btn-primary" name="show" value="Показать">
            </div>
        </form>
    </div>
</div>
@show