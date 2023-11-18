@section('result_schedule')
<div class="mt-3 card" id="result-content">
    <input type="button" class="btn btn-primary" name="download" value="Скачать">
    <h3 id="title-schedule" class="card-header"></h3>
    <table class="table table-striped mt-2">
        <?= $result ?>
    </table>
</div>
@show