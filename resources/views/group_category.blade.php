@section('group_category') 
<div class="card tab-pane fade" id="group-tab-pane" role="tabpanel" aria-labelledby="group-tab" tabindex="-1">
    <h5 class="card-header">Расписание для групп</h5>
    <div class="card-body">
        <form method="post" action="http://istu-rasp/groups_faculty" id="faculties">
            @csrf
            <div class="row">
                <label for="faculty" class="form-label">Выберите институт/факультет</label>
                <select class="form-select mb-3" name="faculty">
                    <option disabled="" selected="">Выберите институт/факультет</option>
                    @foreach($faculties as $faculty)
                        @if($faculty_id != 0 && $faculty_id == $faculty['id'])
                            <option selected value="{{$faculty['id']}}" >{{$faculty['nameFaculty']}}</option>
                        @else <option value="{{$faculty['id']}}" >{{$faculty['nameFaculty']}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="row">
                <label class="form-label">Выберите курс</label>
                <select class="form-select mb-3" name="course">
                    <option disabled="" selected="">Выберите курс</option>
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

            <div class="row">
                <label class="form-label">Выберите группу</label>
                <select class="form-select mb-3" name="group">
                    <option disabled="" selected="">Выберите группу</option>
                    @foreach($groups as $group)
                        @if($group_id != 0 && $group_id == $group['id'])
                            <option selected value="{{$group['id']}}" >{{$group['nameGroup']}}</option>
                        @else <option value="{{$group['id']}}" >{{$group['nameGroup']}}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="row">
                <input type="submit" class="btn btn-primary" name="show" value="Показать">
            </div>
        </form>
    </div>
</div>
@endsection
