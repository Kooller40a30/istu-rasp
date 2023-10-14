@section('teacher_category') 
<div class="card tab-pane fade" id="teacher-tab-pane" role="tabpanel" aria-labelledby="teacher-tab" tabindex="-1">
    <h5 class="card-header">Расписание для преподавателей</h5>
    <div class="card-body">
        <form method="post" action="http://istu-rasp/teachers_faculty" id="faculties">
            <input type="hidden" name="_token" value="xs4LspdSTW0zRHPT2XkaFUdju7KuduuoUHAtOdii">
            <div class="row">
                <label for="teacher" class="form-label">Выберите институт/факультет</label>
                <select class="form-select mb-3" name="faculty" id="faculty">
                    <option disabled="" selected="">Выберите институт/факультет
                    </option>
                </select>
            </div>

            <div class="row">
                <label for="department" class="form-label">Выберите кафедру</label>
                <select class="form-select mb-3" name="department">
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