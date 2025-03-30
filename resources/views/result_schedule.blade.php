@section('result_schedule')
<div class="mt-3 card" id="result-content">
    <button id="downloadButton" class="btn btn-primary">Скачать</button>
    <p class="card-header"><?= $header ?></p>
    <table id="table" class="table table-sm table-striped mt-2" style="overflow-y:scroll;height:900px;display:block;">
        <?= $result ?>
    </table>
    <script>
        document.getElementById('downloadButton').addEventListener('click', function () {
            const table = document.getElementById('table');
            const html = `
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        table, td, th {
                            border: 1px solid #ccc;
                            border-collapse: collapse;
                            padding: 4px;
                            text-align: center;
                        }
                        td.lab { background-color: ${document.getElementById('color-lab').value}; }
                        td.lecture { background-color: ${document.getElementById('color-lecture').value}; }
                        td.practice { background-color: ${document.getElementById('color-practice').value}; }
                    </style>
                </head>
                <body>
                    ${table.outerHTML}
                </body>
                </html>
            `;
    
            const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'расписание.xls';
            link.click();
            URL.revokeObjectURL(url);
        });
    </script>    
</div>

<script>
    function updateScheduleColors() {
        const lectureColor = document.getElementById('color-lecture').value;
        const practiceColor = document.getElementById('color-practice').value;
        const labColor = document.getElementById('color-lab').value;

        document.querySelectorAll('td.lecture').forEach(td => td.style.backgroundColor = lectureColor);
        document.querySelectorAll('td.practice').forEach(td => td.style.backgroundColor = practiceColor);
        document.querySelectorAll('td.lab').forEach(td => td.style.backgroundColor = labColor);
    }

    // Привязка событий
    document.getElementById('color-lecture').addEventListener('input', updateScheduleColors);
    document.getElementById('color-practice').addEventListener('input', updateScheduleColors);
    document.getElementById('color-lab').addEventListener('input', updateScheduleColors);

    // Первый вызов для начальной установки
    updateScheduleColors();
</script>

@show