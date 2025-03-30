@section('room_category')
<div class="card tab-pane fade" id="room-tab-pane" role="tabpanel" aria-labelledby="room-tab" tabindex="-1">
    <h5 class="card-header">Расписание по аудиториям</h5>
    <div class="card-body">
        <form method="post" action="http://istu-rasp/classrooms_faculty">
            @csrf
            <div class="row">
                <label for="faculty" class="form-label">Выберите институт/факультет</label>
                <select class="form-select mb-3" name="faculty" id="room_faculty">
                    <option disabled selected>Выберите институт/факультет</option>
                    @foreach($facultiesRoom as $faculty)                        
                        <option value="{{$faculty->id}}">{{$faculty->nameFaculty}}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <label class="form-label">Выберите кафедру</label>
                <select class="form-select mb-3" name="department" id="room_department">
                    <option disabled selected>Выберите кафедру</option>
                    @foreach($deps as $dep)                        
                        <option value="{{$dep->id}}">{{$dep->nameDepartment}}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <label for="classroom" class="form-label">Выберите аудиторию</label>
                <select class="form-select mb-3" name="classroom" id="classroom">
                    <option disabled selected>Выберите аудиторию</option>
                    @foreach($classrooms as $classroom)                        
                        <option value="{{$classroom->id}}">{{$classroom->numberClassroom}}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <button type="submit" class="btn btn-primary" id="btn-room">
                    <span class="btn-text">Показать</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@show