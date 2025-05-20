<?php

namespace App\Exports;

use App\Models\Lokasi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LokasiExport
{
    /**
     * Export data to Excel file
     * 
     * @param string $filePath Path where the Excel file will be saved
     * @return void
     */
    public function export($filePath)
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Lokasi');
        
        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nama Lokasi');
        $sheet->setCellValue('C1', 'Latitude');
        $sheet->setCellValue('D1', 'Longitude');
     
        
        // Style the header row
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        
        // Get data
        $data = $this->getData();
        
        // Fill data starting from row 2
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['nama_lokasi']);
            $sheet->setCellValue('C' . $row, $item['latitude']);
            $sheet->setCellValue('D' . $row, $item['longitude']);
            
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create writer and save file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }
    
    /**
     * Get formatted data for export
     * 
     * @return array
     */
    private function getData()
    {
        return Lokasi::all()->map(function ($lokasi) {
            return [
                'id' => $lokasi->id,
                'nama_lokasi' => $lokasi->nama_lokasi ?? '',
                'latitude' => $lokasi->latitude ?? '',
                'longitude' => $lokasi->longitude ?? '',
              
            ];
        })->toArray();
    }
} 