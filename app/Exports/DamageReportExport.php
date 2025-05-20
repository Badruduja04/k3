<?php

namespace App\Exports;

use App\Models\Pelaporan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DamageReportExport
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
        $sheet->setCellValue('A1', 'Laporan Kerusakan Barang');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set period
        $period = \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->format('F Y');
        $sheet->setCellValue('A2', 'Periode: ' . $period);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = ['No', 'Tanggal', 'Nama Barang', 'Lokasi', 'Deskripsi Kerusakan', 'Status'];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:F4')->getFont()->setBold(true);
        $sheet->getStyle('A4:F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Get data
        $damages = Pelaporan::whereHas('statusrelation', function($q) {
            $q->where('nama_status', 'kerusakan');
        })
            ->whereMonth('waktu', $this->month)
            ->whereYear('waktu', $this->year)
            ->with(['barang', 'lokasi', 'statusrelation'])
            ->get();

        // Jika ingin mengelompokkan berdasarkan status (misal, jika ada lebih dari satu status)
        $grouped = $damages->groupBy(function($item) {
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
            foreach ($items as $damage) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $damage->waktu ? \Carbon\Carbon::parse($damage->waktu)->format('d/m/Y') : '');
                $sheet->setCellValue('C' . $row, $damage->barang ? $damage->barang->nama_barang : '');
                $sheet->setCellValue('D' . $row, $damage->lokasi ? $damage->lokasi->nama_lokasi : '');
                $sheet->setCellValue('E' . $row, $damage->keterangan);
                $sheet->setCellValue('F' . $row, $damage->statusrelation ? $damage->statusrelation->nama_status : '');
                $row++;
            }
        }

        // Style the data
        $lastRow = $row - 1;
        $sheet->getStyle('A5:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E5:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Add borders
        $sheet->getStyle('A4:F' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save the file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }
} 