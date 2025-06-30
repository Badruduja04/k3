<?php

namespace App\Exports;

use App\Models\Pelaporan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LossReportExport
{
    protected $month;
    protected $year;

    public function __construct($month = null, $year = null)
    {
        $this->month = $month ?: date('m');
        $this->year = $year ?: date('Y');
    }

    public function export($filePath)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'Laporan Kehilangan Barang');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set period
        $period = \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->format('F Y');
        $sheet->setCellValue('A2', 'Periode: ' . $period);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = ['No', 'Tanggal', 'Nama Barang', 'Lokasi', 'Deskripsi Kehilangan', 'Status'];
        $sheet->fromArray($headers, null, 'A4');
        
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
        $sheet->getStyle('A4:F4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(30);

        // Get data
        $losses = Pelaporan::whereHas('statusrelation', function($q) {
            $q->where('nama_status', 'kehilangan');
        })
            ->whereMonth('waktu', $this->month)
            ->whereYear('waktu', $this->year)
            ->with(['barang', 'lokasi', 'statusrelation'])
            ->get();

        // Jika ingin mengelompokkan berdasarkan status (misal, jika ada lebih dari satu status)
        $grouped = $losses->groupBy(function($item) {
            return $item->statusrelation ? $item->statusrelation->nama_status : 'Tanpa Status';
        });

        $row = 5;
        $no = 1;
        foreach ($grouped as $status => $items) {
            // Jika ingin menampilkan judul status di setiap kelompok
            if (count($grouped) > 1) {
                $sheet->setCellValue('A' . $row, strtoupper($status));
                $sheet->mergeCells('A'.$row.':F'.$row);
                $sheet->getStyle('A'.$row.':F'.$row)->getFont()->setBold(true);
                $row++;
            }
            foreach ($items as $loss) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $loss->waktu ? \Carbon\Carbon::parse($loss->waktu)->format('d/m/Y') : '');
                $sheet->setCellValue('C' . $row, $loss->barang ? $loss->barang->nama_barang : '');
                $sheet->setCellValue('D' . $row, $loss->lokasi ? $loss->lokasi->nama_lokasi : '');
                $sheet->setCellValue('E' . $row, $loss->keterangan);
                $sheet->setCellValue('F' . $row, $loss->statusrelation ? $loss->statusrelation->nama_status : '');
                
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
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($dataStyle);
                $sheet->getRowDimension($row)->setRowHeight(25);
                
                // Center align specific columns
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $row++;
            }
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);   // No
        $sheet->getColumnDimension('B')->setWidth(15);  // Tanggal
        $sheet->getColumnDimension('C')->setWidth(30);  // Nama Barang
        $sheet->getColumnDimension('D')->setWidth(25);  // Lokasi
        $sheet->getColumnDimension('E')->setWidth(35);  // Deskripsi Kehilangan
        $sheet->getColumnDimension('F')->setWidth(15);  // Status

        // Save the file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }
}