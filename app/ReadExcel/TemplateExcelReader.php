<?php

namespace App\ReadExcel;

use App\Models\Schedule;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TemplateExcelReader 
{
    public function processFiles($files)
    {
        Schedule::where('id', '>', 0)->delete();
        foreach ($files as $file) {
            $this->processFile($file);
        }
    }

    public function processFile($file)
    {
        $path = GetPath::savePath($file);
        $reader = IOFactory::createReaderForFile($path);
        $spreadsheet = $reader->load($path);
        $sheets = $spreadsheet->getAllSheets();

        $oldParser = new OldExcelParser();
        $parser = new ExcelParser();

        foreach ($sheets as $n => $sheet) {
            // костыль с if-ами
            if ($sheet->getCell('B2')->getValue() != 'Группа') {
                $oldParser->processSheet($sheet);
            } else {
                $parser->processSheet($sheet);
            }
        }
    }
}