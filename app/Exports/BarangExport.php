<?php

namespace App\Exports;

use App\Models\Barang;
use App\Models\Lokasi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
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
            $dataStyle = [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($dataStyle);
            $sheet->getRowDimension($row)->setRowHeight(25);
            
            // Center align specific columns
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row++;
        }
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);   // ID
        $sheet->getColumnDimension('B')->setWidth(30);  // Nama Barang
        $sheet->getColumnDimension('C')->setWidth(25);  // Lokasi
        $sheet->getColumnDimension('D')->setWidth(20);  // QR Code
        $sheet->getColumnDimension('E')->setWidth(20);  // Created At
        $sheet->getColumnDimension('F')->setWidth(20);  // Updated At
        
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