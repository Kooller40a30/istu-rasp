@section('upload_files_page')
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="uploadModalLabel">Загрузите файлы</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>            
            <div class="modal-body">
                <form id="formFile" class="" method="POST" action="{{ url('/upload_files') }}" enctype="multipart/form-data">
                    @csrf
                    @if($errors->any())
                    <div class="alert alert-danger container" style="height: 55px;">
                        <ul>
                            @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="mess"> </div>

                    @foreach($typesFile as $key => $type)
                    <div class="form-check">
                        <input class="form-check-input" value="{{$key}}" type="radio" name="type" id="typeFile{{$key}}">
                        <label class="form-check-label" for="typeFile{{$key}}">
                            {{$type}}
                        </label>
                    </div>
                    @endforeach

                    <input class="form-control bg-secondary-subtle mb-lg-4 mt-3 mb-3" name="import_file[]" type="file" id="ExcelFile" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" multiple/>
                    <input type="submit" class="btn btn-primary mb-lg-4 mb-3" style="font-size: 20px" value="Загрузить" />
                    <div class="form-group">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated " role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $(document).ready(function() {
            let mess = $('.mess');
            $('#formFile').ajaxForm({
                beforeSend: function() {
                    var percentage = '0';
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentage = percentComplete;
                    $('.progress .progress-bar').css("width", percentage + '%', function() {
                        return $(this).attr("aria-valuenow", percentage) + "%";
                    })
                },
                complete: function(xhr) {
                    console.log('File has uploaded');
                    mess.html('<div class = "alert alert-success">Файлы загружены</div>');
                    $('.progress .progress-bar').css("width", 0 + '%', function() {
                        return $(this).attr("aria-valuenow", 0) + "%";
                    })
                }
            });
        });
    });
</script>
@show