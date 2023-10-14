@section('room_category')
<div class="card tab-pane fade" id="room-tab-pane" role="tabpanel" aria-labelledby="room-tab" tabindex="-1">
    <h5 class="card-header">Расписание по аудиториям</h5>
    <div class="card-body">
        <form method="post" action="http://istu-rasp/classrooms_faculty" id="faculties">
            <div class="row">
                <label for="faculty" class="form-label">Выберите институт/факультет</label>
                <select class="form-select mb-3" name="faculty" id="faculty">
                    <option disabled="" selected="">Выберите институт/факультет
                    </option>
                    <option value="4">Институт «Информатика и вычислительная
                        техника»</option>
                </select>
            </div>

            <div class="row">
                <label class="form-label">Выберите кафедру</label>
                <select class="form-select mb-3" name="department" id="department">
                    <option disabled="" selected="">Выберите кафедру</option>
                    <option value="34.4">Автоматизированные системы обработки
                        информации и управления (АСОИУ)</option>
                </select>
            </div>

            <div class="row">
                <label for="classroom" class="form-label">Выберите аудиторию</label>
                <select class="form-select mb-3" name="classroom" id="classroom">
                    <option disabled="" selected="">Выберите аудиторию</option>
                </select>
            </div>

            <div class="row">
                <input type="submit" class="btn btn-primary" name="show" value="Показать">
            </div>

        </form>
    </div>
</div>
@endsection