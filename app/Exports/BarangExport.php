<?php

namespace App\Exports;

use App\Models\Barang;
use App\Models\Lokasi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BarangExport
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
        $sheet->setTitle('Data Barang');
        
        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Lokasi');
        $sheet->setCellValue('D1', 'QR Code');
        $sheet->setCellValue('E1', 'Created At');
        $sheet->setCellValue('F1', 'Updated At');
        
        // Style the header row
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        
        // Get data
        $data = $this->getData();
        
        // Fill data starting from row 2
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['nama_barang']);
            $sheet->setCellValue('C' . $row, $item['lokasi']);
            $sheet->setCellValue('D' . $row, $item['qr_code']);
            $sheet->setCellValue('E' . $row, $item['created_at']);
            $sheet->setCellValue('F' . $row, $item['updated_at']);
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
        return Barang::all()->map(function ($item) {
            // Get the location name directly from the Lokasi table
            $lokasiName = '';
            if ($item->id_lokasi) {
                $lokasi = Lokasi::find($item->id_lokasi);
                if ($lokasi) {
                    $lokasiName = $lokasi->nama_lokasi;
                }
            }
            
            // Format dates appropriately
            $createdAt = '';
            if (is_object($item->created_at) && method_exists($item->created_at, 'format')) {
                $createdAt = $item->created_at->format('d/m/Y H:i:s');
            } elseif (is_string($item->created_at) && !empty($item->created_at)) {
                try {
                    $date = new \DateTime($item->created_at);
                    $createdAt = $date->format('d/m/Y H:i:s');
                } catch (\Exception $e) {
                    $createdAt = $item->created_at;
                }
            }
            
            $updatedAt = '';
            if (is_object($item->updated_at) && method_exists($item->updated_at, 'format')) {
                $updatedAt = $item->updated_at->format('d/m/Y H:i:s');
            } elseif (is_string($item->updated_at) && !empty($item->updated_at)) {
                try {
                    $date = new \DateTime($item->updated_at);
                    $updatedAt = $date->format('d/m/Y H:i:s');
                } catch (\Exception $e) {
                    $updatedAt = $item->updated_at;
                }
            }
            
            return [
                'id' => $item->id,
                'nama_barang' => $item->nama_barang ?? '',
                'lokasi' => $lokasiName,
                'qr_code' => $item->qr ?? '',
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
        })->toArray();
    }
} 