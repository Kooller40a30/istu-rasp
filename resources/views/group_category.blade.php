@section('group_category') 
<div class="card tab-pane fade" id="group-tab-pane" role="tabpanel" aria-labelledby="group-tab" tabindex="-1">
    <h5 class="card-header">Расписание для групп</h5>
    <div class="card-body">
        <form method="post" action="http://istu-rasp/groups_faculty" id="faculties">
            <div class="row">
                <label for="faculty" class="form-label">Выберите институт/факультет</label>
                <select class="form-select mb-3" name="faculty">
                    <option disabled="" selected="">Выберите институт/факультет</option>

                </select>
            </div>
            <div class="row">
                <label class="form-label">Выберите курс</label>
                <select class="form-select mb-3" name="course">
                    <option disabled="" selected="">Выберите курс</option>

                </select>
            </div>

            <div class="row">
                <label class="form-label">Выберите группу</label>
                <select class="form-select mb-3" name="group">
                    <option disabled="" selected="">Выберите группу</option>

                </select>
            </div>

            <div class="row">
                <input type="submit" class="btn btn-primary" name="show" value="Показать">
            </div>
        </form>
    </div>
</div>
@show
