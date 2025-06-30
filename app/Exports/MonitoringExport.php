<?php

namespace App\Exports;

use App\Models\Monitoring;
use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\User;
use App\Models\Status;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonitoringExport
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
        $sheet->setTitle('Data Monitoring');
        
        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Lokasi');
        $sheet->setCellValue('D1', 'User');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Keterangan');
        $sheet->setCellValue('G1', 'Tanggal');
        $sheet->setCellValue('H1', 'Created At');
        $sheet->setCellValue('I1', 'Updated At');
        
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
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Get data
        $data = $this->getData();
        
        // Fill data starting from row 2
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['nama_barang']);
            $sheet->setCellValue('C' . $row, $item['lokasi']);
            $sheet->setCellValue('D' . $row, $item['user']);
            $sheet->setCellValue('E' . $row, $item['status']);
            $sheet->setCellValue('F' . $row, $item['keterangan']);
            $sheet->setCellValue('G' . $row, $item['tanggal']);
            $sheet->setCellValue('H' . $row, $item['created_at']);
            $sheet->setCellValue('I' . $row, $item['updated_at']);
            // Style data rows
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
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($dataStyle);
            $sheet->getRowDimension($row)->setRowHeight(25);
            
            // Center align specific columns
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row++;
        }
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);  // ID
        $sheet->getColumnDimension('B')->setWidth(30); // Nama Barang
        $sheet->getColumnDimension('C')->setWidth(25); // Lokasi
        $sheet->getColumnDimension('D')->setWidth(20); // User
        $sheet->getColumnDimension('E')->setWidth(15); // Status
        $sheet->getColumnDimension('F')->setWidth(35); // Keterangan
        $sheet->getColumnDimension('G')->setWidth(20); // Tanggal
        $sheet->getColumnDimension('H')->setWidth(20); // Created At
        $sheet->getColumnDimension('I')->setWidth(20); // Updated At
        
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
        return Monitoring::with(['barang', 'user', 'statusRelation'])->get()->map(function ($item) {
            // Get the barang name
            $barangName = '';
            if ($item->barang) {
                $barangName = $item->barang->nama_barang;
            }
            
            // Get the location name
            $lokasiName = '';
            if ($item->barang && $item->barang->lokasi) {
                $lokasiName = $item->barang->lokasi->nama_lokasi;
            }
            
            // Get the user name
            $userName = '';
            if ($item->user) {
                $userName = $item->user->nama;
            }
            
            // Get the status name
            $statusName = '';
            if ($item->statusRelation) {
                $statusName = $item->statusRelation->nama_status;
            }
            
            // Format dates appropriately
            $tanggal = '';
            if (is_object($item->tanggal) && method_exists($item->tanggal, 'format')) {
                $tanggal = $item->tanggal->format('d/m/Y H:i:s');
            } elseif (is_string($item->tanggal) && !empty($item->tanggal)) {
                try {
                    $date = new \DateTime($item->tanggal);
                    $tanggal = $date->format('d/m/Y H:i:s');
                } catch (\Exception $e) {
                    $tanggal = $item->tanggal;
                }
            }
            
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
                'nama_barang' => $barangName,
                'lokasi' => $lokasiName,
                'user' => $userName,
                'status' => $statusName,
                'keterangan' => $item->keterangan ?? '',
                'tanggal' => $tanggal,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
        })->toArray();
    }
}