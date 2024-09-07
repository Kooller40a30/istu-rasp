@section('result_schedule')
<div class="mt-3 card" id="result-content">
    <button id="downloadButton" class="btn btn-primary">Скачать</button>
    <p class="card-header"><?= $header ?></p>
    <table id="table" class="table table-sm table-striped mt-2" style="overflow-y:scroll;height:900px;display:block;">
        <?= $result ?>
    </table>
    <script>
        // JavaScript-код для обработки события нажатия на кнопку
        document.getElementById('downloadButton').addEventListener('click', function() {
            // Получаем HTML-код таблицы
            var table = document.getElementById('table');
            var tableHtml = table.outerHTML;

            // Создаем новый Excel-документ
            var workbook = XLSX.utils.book_new();
            var ws = XLSX.utils.table_to_sheet(table);

            // Добавляем таблицу в документ
            XLSX.utils.book_append_sheet(workbook, ws, 'Sheet1');

            // Сохраняем документ в файл
            XLSX.writeFile(workbook, 'output.xlsx');
        });
    </script>
</div>

@show