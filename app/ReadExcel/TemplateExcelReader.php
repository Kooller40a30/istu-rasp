<?php

namespace App\ReadExcel;

use App\Models\Schedule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Log;

/**
 * Класс для чтения и обработки шаблонных Excel файлов расписания.
 */
class TemplateExcelReader
{
    /**
     * Обрабатывает массив файлов расписания.
     *
     * @param array $files Массив файлов для обработки.
     * @return void
     */
    public function processFiles(array $files): void
    {
        Schedule::where('id', '>', 0)->delete();

        foreach ($files as $file) {
            try {
                $this->processFile($file);
            } catch (\Exception $e) {
                // Логирование ошибки обработки файла и продолжение обработки остальных файлов
                Log::error("Ошибка при обработке файла {$file}: " . $e->getMessage());
            }
        }
    }

    /**
     * Обрабатывает отдельный файл расписания.
     *
     * @param mixed $file Файл для обработки.
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception Если не удалось прочитать файл.
     */
    public function processFile($file): void
    {
        $path = GetPath::savePath($file);
        $reader = IOFactory::createReaderForFile($path);
        $spreadsheet = $reader->load($path);
        $sheets = $spreadsheet->getAllSheets();

        // Инициализация парсеров
        $oldParser = new OldExcelParser(basename($path)); // TODO: Избавиться от передачи имени файла в парсер
        $newParser = new ExcelParser(basename($path));    // TODO: Добавить обработку исключений здесь

        foreach ($sheets as $n => $sheet) {
            // костыль с if-ами
            if ($sheet->getCell('B2')->getValue() != 'Группа') {
                $oldParser->processSheet($sheet);
            } else {
                $newParser->processSheet($sheet);
            }
        }
    }
}
