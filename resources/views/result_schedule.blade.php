@section('result_schedule')
<div class="mt-3 card" id="result-content">
    <input type="button" class="btn btn-primary" name="download" value="Скачать">
    <p class="card-header"><?= $header ?></p>
    <table class="table table-sm table-striped mt-2" style="overflow-y:scroll;height:900px;display:block;">
        <?= $result ?>
    </table>
</div>
@show