<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use Illuminate\Support\Facades\Log;

class ExcelProcessor
{
    public function process($filePath)
    {
        try
        {
            $spreadsheet = IOFactory::load($filePath);
            $allData = [];

            foreach($spreadsheet->getAllSheets() as $sheet) {
                $sheetName=$sheet->getTitle();
                $sheetData=$this->processSheet($sheet);
                $allData[$sheetName] = $sheetData;
            }
            return $allData;
        }
        catch (\Exception $e)
        {
            Log::error('Error processing Excel file: ' . $e->getMessage());
            throw new \Exception('Error processing Excel file: ' . $e->getMessage());
        }
    }

    private function processSheet($sheet)
    {
        $data=[];
        $highestRow=$sheet->getHighestRow();
        $highestColumn=$sheet->getHighestColumn();
        $highestColumnIndex=\PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        // Assuming the first row contains headers
        try
        {
            for($col=0;;$col++)
            {
                $value=$sheet->getCell(chr(65+$col).'1')->getValue();
                if (!$value) break;
                $headers[]=$value;
            }
            for($row=2;;$row++)
            {
                $rowData=[];
                $col=0;
                $value=$sheet->getCell(chr(65+$col).$row)->getCalculatedValue();
                if (!$value) break;
                $rowData[$headers[$col]]=$value;
                $col++;
                for(;;$col++)
                {
                    $value=$sheet->getCell(chr(65+$col).$row)->getCalculatedValue();
                    if ($value=="") break;
                    $rowData[$headers[$col]]=$value;
                }
                $data[]=$rowData;
            }
            return $data;

        }
        catch (\Exception $e)
        {
            Log::error('Error processing Excel sheet: ' . $e->getMessage());
            throw new \Exception('Error processing Excel sheet: ' . $e->getMessage());
        }

    }
}
