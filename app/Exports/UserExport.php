<?php

namespace App\Exports;

use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UserExport
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
        $sheet->setTitle('Data Users');
        
        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Created At');
        $sheet->setCellValue('E1', 'Updated At');
        
        // Style headers
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
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Get data
        $data = $this->getData();
        
        // Fill data starting from row 2
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['nama']);
            $sheet->setCellValue('C' . $row, $item['email']);
            $sheet->setCellValue('D' . $row, $item['created_at']);
            $sheet->setCellValue('E' . $row, $item['updated_at']);
            
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
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($dataStyle);
            $sheet->getRowDimension($row)->setRowHeight(25);
            
            // Center align specific columns
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row++;
        }
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);   // ID
        $sheet->getColumnDimension('B')->setWidth(30);  // Nama
        $sheet->getColumnDimension('C')->setWidth(35);  // Email
        $sheet->getColumnDimension('D')->setWidth(20);  // Created At
        $sheet->getColumnDimension('E')->setWidth(20);  // Updated At
        
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
        return User::all()->map(function ($user) {
            // Format dates appropriately
            $createdAt = '';
            if (is_object($user->created_at) && method_exists($user->created_at, 'format')) {
                $createdAt = $user->created_at->format('d/m/Y H:i:s');
            } elseif (is_string($user->created_at) && !empty($user->created_at)) {
                try {
                    $date = new \DateTime($user->created_at);
                    $createdAt = $date->format('d/m/Y H:i:s');
                } catch (\Exception $e) {
                    $createdAt = $user->created_at;
                }
            }
            
            $updatedAt = '';
            if (is_object($user->updated_at) && method_exists($user->updated_at, 'format')) {
                $updatedAt = $user->updated_at->format('d/m/Y H:i:s');
            } elseif (is_string($user->updated_at) && !empty($user->updated_at)) {
                try {
                    $date = new \DateTime($user->updated_at);
                    $updatedAt = $date->format('d/m/Y H:i:s');
                } catch (\Exception $e) {
                    $updatedAt = $user->updated_at;
                }
            }
            
            return [
                'id' => $user->id,
                'nama' => $user->nama ?? '',
                'email' => $user->email ?? '',
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
        })->toArray();
    }
}