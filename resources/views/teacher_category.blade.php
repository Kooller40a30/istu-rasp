@section('teacher_category') 
<div class="card tab-pane fade" id="teacher-tab-pane" role="tabpanel" aria-labelledby="teacher-tab" tabindex="-1">
    <h5 class="card-header">Расписание для преподавателей</h5>
    <div class="card-body">
        <form method="post" action="http://istu-rasp/teachers_faculty">
            @csrf
            <div class="row">
                <label for="teacher_faculty" class="form-label">Выберите институт/факультет</label>
                <select class="form-select mb-3 selectize-js" name="faculty" id="teacher_faculty">
                    <option disabled selected>Выберите институт/факультет</option>
                    @foreach($facultiesTeacher as $faculty)
                        <option value="{{$faculty->id}}">{{$faculty->nameFaculty}}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <label for="department" class="form-label">Выберите кафедру</label>
                <select class="form-select mb-3 selectize-js" name="department" id="department">
                    <option disabled selected>Выберите кафедру</option>
                    @foreach($deps as $dep)                        
                        <option value="{{$dep->id}}">{{$dep->nameDepartment}}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <label for="teacher" class="form-label">Выберите преподавателя</label>
                <select class="form-select mb-3 selectize-js" name="teacher" id="teacher">
                    <option disabled selected>Выберите преподавателя</option>
                    @foreach($teachers as $teacher)                        
                        <option value="{{$teacher->id}}">{{$teacher->nameTeacher}}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary" id="btn-teacher">
                    <span class="btn-text">Показать</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@show