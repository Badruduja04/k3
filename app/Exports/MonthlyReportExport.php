<?php

namespace App\Exports;

use App\Models\Monitoring;
use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\User;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonthlyReportExport
{
    protected $month;
    protected $year;

    /**
     * Constructor to set the month and year for the report
     * 
     * @param int|null $month The month (1-12), or null for current month
     * @param int|null $year The year, or null for current year
     */
    public function __construct($month = null, $year = null)
    {
        $this->month = $month ?: date('m');
        $this->year = $year ?: date('Y');
    }

    /**
     * Export monthly report to Excel file
     * 
     * @param string $filePath Path where the Excel file will be saved
     * @return void
     */
    public function export($filePath)
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        // Create Summary sheet
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Summary');
        $this->createSummarySheet($summarySheet);
        
        // Create Monitoring sheet
        $monitoringSheet = $spreadsheet->createSheet();
        $monitoringSheet->setTitle('Monitoring');
        $this->createMonitoringSheet($monitoringSheet);
        
        // Create Barang sheet
        $barangSheet = $spreadsheet->createSheet();
        $barangSheet->setTitle('Barang');
        $this->createBarangSheet($barangSheet);
        
        // Create Lokasi sheet
        $lokasiSheet = $spreadsheet->createSheet();
        $lokasiSheet->setTitle('Lokasi');
        $this->createLokasiSheet($lokasiSheet);
        
        // Create Users sheet
        $usersSheet = $spreadsheet->createSheet();
        $usersSheet->setTitle('Users');
        $this->createUsersSheet($usersSheet);
        
        // Set the first sheet as active
        $spreadsheet->setActiveSheetIndex(0);
        
        // Create writer and save file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }
    
    /**
     * Create the summary sheet with monthly statistics
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    protected function createSummarySheet($sheet)
    {
        $monthName = Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->translatedFormat('F Y');
        
        // Set title
        $sheet->setCellValue('A1', 'LAPORAN BULANAN - ' . strtoupper($monthName));
        $sheet->mergeCells('A1:H1');
        
        // Style the title
        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add date generated
        $sheet->setCellValue('A3', 'Tanggal Dibuat:');
        $sheet->setCellValue('B3', Carbon::now()->format('d/m/Y H:i'));
        
        // Section: Monthly statistics
        $sheet->setCellValue('A5', 'STATISTIK BULANAN');
        $sheet->mergeCells('A5:H5');
        $sheet->getStyle('A5:H5')->getFont()->setBold(true)->setSize(14);
        
        // Get statistics
        $stats = $this->getMonthlyStatistics();
        
        // Add statistics
        $sheet->setCellValue('A7', 'Total Monitoring:');
        $sheet->setCellValue('B7', $stats['total_monitoring']);
        
        $sheet->setCellValue('A8', 'Total Barang:');
        $sheet->setCellValue('B8', $stats['total_barang']);
        
        $sheet->setCellValue('A9', 'Total Lokasi:');
        $sheet->setCellValue('B9', $stats['total_lokasi']);
        
        $sheet->setCellValue('A10', 'Total User:');
        $sheet->setCellValue('B10', $stats['total_users']);
        
        // Add status breakdown
        $sheet->setCellValue('D7', 'Status Sesuai:');
        $sheet->setCellValue('E7', $stats['status_sesuai']);
        
        $sheet->setCellValue('D8', 'Status Rusak:');
        $sheet->setCellValue('E8', $stats['status_rusak']);
        
        $sheet->setCellValue('D9', 'Status Hilang:');
        $sheet->setCellValue('E9', $stats['status_hilang']);
        
        $sheet->setCellValue('D10', 'Status Lainnya:');
        $sheet->setCellValue('E10', $stats['status_lainnya']);
        
        // Section: Top locations
        $sheet->setCellValue('A12', 'LOKASI DENGAN AKTIVITAS TERTINGGI');
        $sheet->mergeCells('A12:H12');
        $sheet->getStyle('A12:H12')->getFont()->setBold(true)->setSize(14);
        
        // Headers for top locations
        $sheet->setCellValue('A14', 'No');
        $sheet->setCellValue('B14', 'Nama Lokasi');
        $sheet->setCellValue('C14', 'Jumlah Monitoring');
        $sheet->getStyle('A14:C14')->getFont()->setBold(true);
        
        // Add top locations data
        $topLocations = $this->getTopLocations();
        $row = 15;
        $count = 1;
        foreach ($topLocations as $loc) {
            $sheet->setCellValue('A' . $row, $count);
            $sheet->setCellValue('B' . $row, $loc['lokasi']);
            $sheet->setCellValue('C' . $row, $loc['count']);
            $row++;
            $count++;
        }
        
        // Section: Recent activity
        $sheet->setCellValue('A' . ($row + 1), 'AKTIVITAS MONITORING TERBARU');
        $sheet->mergeCells('A' . ($row + 1) . ':H' . ($row + 1));
        $sheet->getStyle('A' . ($row + 1) . ':H' . ($row + 1))->getFont()->setBold(true)->setSize(14);
        
        // Headers for recent activity
        $recentRow = $row + 3;
        $sheet->setCellValue('A' . $recentRow, 'Tanggal');
        $sheet->setCellValue('B' . $recentRow, 'Barang');
        $sheet->setCellValue('C' . $recentRow, 'Lokasi');
        $sheet->setCellValue('D' . $recentRow, 'User');
        $sheet->setCellValue('E' . $recentRow, 'Status');
        $sheet->getStyle('A' . $recentRow . ':E' . $recentRow)->getFont()->setBold(true);
        
        // Add recent activity data
        $recentActivities = $this->getRecentActivities();
        $recentRow++;
        foreach ($recentActivities as $activity) {
            $sheet->setCellValue('A' . $recentRow, $activity['tanggal']);
            $sheet->setCellValue('B' . $recentRow, $activity['barang']);
            $sheet->setCellValue('C' . $recentRow, $activity['lokasi']);
            $sheet->setCellValue('D' . $recentRow, $activity['user']);
            $sheet->setCellValue('E' . $recentRow, $activity['status']);
            $recentRow++;
        }
        
        // Auto size columns for better readability
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    /**
     * Create the monitoring data sheet
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    protected function createMonitoringSheet($sheet)
    {
        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Lokasi');
        $sheet->setCellValue('D1', 'User');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Keterangan');
        $sheet->setCellValue('G1', 'Tanggal');
        $sheet->setCellValue('H1', 'Created At');
        
        // Style the header row
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        
        // Get monitoring data for the month
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();
        
        $data = Monitoring::with(['barang.lokasi', 'user', 'statusRelation'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                // Format the data similarly to MonitoringExport
                $barangName = $item->barang ? $item->barang->nama_barang : '';
                $lokasiName = $item->barang && $item->barang->lokasi ? $item->barang->lokasi->nama_lokasi : '';
                $userName = $item->user ? $item->user->nama : '';
                $statusName = $item->statusRelation ? $item->statusRelation->nama_status : '';
                
                // Format date/times
                $tanggal = $item->tanggal ? Carbon::parse($item->tanggal)->format('d/m/Y H:i:s') : '';
                $createdAt = $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y H:i:s') : '';
                
                return [
                    'id' => $item->id,
                    'nama_barang' => $barangName,
                    'lokasi' => $lokasiName,
                    'user' => $userName,
                    'status' => $statusName,
                    'keterangan' => $item->keterangan ?? '',
                    'tanggal' => $tanggal,
                    'created_at' => $createdAt,
                ];
            })->toArray();
        
        // Add data to sheet
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
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    /**
     * Create the barang (items) data sheet
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    protected function createBarangSheet($sheet)
    {
        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Lokasi');
        $sheet->setCellValue('D1', 'QR Code');
        $sheet->setCellValue('E1', 'Created At');
        
        // Style the header row
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        
        // Get barang data
        $data = Barang::with('lokasi')->get()->map(function ($item) {
            $lokasiName = $item->lokasi ? $item->lokasi->nama_lokasi : '';
            $createdAt = $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y H:i:s') : '';
            
            return [
                'id' => $item->id,
                'nama_barang' => $item->nama_barang ?? '',
                'lokasi' => $lokasiName,
                'qr_code' => $item->qr ?? '',
                'created_at' => $createdAt,
            ];
        })->toArray();
        
        // Add data to sheet
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['nama_barang']);
            $sheet->setCellValue('C' . $row, $item['lokasi']);
            $sheet->setCellValue('D' . $row, $item['qr_code']);
            $sheet->setCellValue('E' . $row, $item['created_at']);
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    /**
     * Create the lokasi (locations) data sheet
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    protected function createLokasiSheet($sheet)
    {
        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nama Lokasi');
        $sheet->setCellValue('C1', 'Latitude');
        $sheet->setCellValue('D1', 'Longitude');
        $sheet->setCellValue('E1', 'Created At');
        
        // Style the header row
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        
        // Get lokasi data
        $data = Lokasi::all()->map(function ($item) {
            $createdAt = $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y H:i:s') : '';
            
            return [
                'id' => $item->id,
                'nama_lokasi' => $item->nama_lokasi ?? '',
                'latitude' => $item->latitude ?? '',
                'longitude' => $item->longitude ?? '',
                'created_at' => $createdAt,
            ];
        })->toArray();
        
        // Add data to sheet
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['nama_lokasi']);
            $sheet->setCellValue('C' . $row, $item['latitude']);
            $sheet->setCellValue('D' . $row, $item['longitude']);
            $sheet->setCellValue('E' . $row, $item['created_at']);
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    /**
     * Create the users data sheet
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    protected function createUsersSheet($sheet)
    {
        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Created At');
        
        // Style the header row
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        
        // Get users data
        $data = User::all()->map(function ($item) {
            $createdAt = $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y H:i:s') : '';
            
            return [
                'id' => $item->id,
                'nama' => $item->nama ?? '',
                'email' => $item->email ?? '',
                'created_at' => $createdAt,
            ];
        })->toArray();
        
        // Add data to sheet
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['id']);
            $sheet->setCellValue('B' . $row, $item['nama']);
            $sheet->setCellValue('C' . $row, $item['email']);
            $sheet->setCellValue('D' . $row, $item['created_at']);
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    /**
     * Get monthly statistics
     * 
     * @return array
     */
    protected function getMonthlyStatistics()
    {
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();
        
        // Get total monitoring for the month
        $totalMonitoring = Monitoring::whereBetween('tanggal', [$startDate, $endDate])->count();
        
        // Get status breakdown
        $statusSesuai = Monitoring::whereBetween('tanggal', [$startDate, $endDate])
            ->whereHas('statusRelation', function($q) {
                $q->where('nama_status', 'sesuai');
            })->count();
            
        $statusRusak = Monitoring::whereBetween('tanggal', [$startDate, $endDate])
            ->whereHas('statusRelation', function($q) {
                $q->where('nama_status', 'rusak');
            })->count();
            
        $statusHilang = Monitoring::whereBetween('tanggal', [$startDate, $endDate])
            ->whereHas('statusRelation', function($q) {
                $q->where('nama_status', 'hilang');
            })->count();
            
        $statusLainnya = $totalMonitoring - $statusSesuai - $statusRusak - $statusHilang;
        
        return [
            'total_monitoring' => $totalMonitoring,
            'total_barang' => Barang::count(),
            'total_lokasi' => Lokasi::count(),
            'total_users' => User::count(),
            'status_sesuai' => $statusSesuai,
            'status_rusak' => $statusRusak,
            'status_hilang' => $statusHilang,
            'status_lainnya' => max(0, $statusLainnya),
        ];
    }
    
    /**
     * Get top locations by monitoring activity
     * 
     * @return array
     */
    protected function getTopLocations()
    {
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();
        
        // Get top locations by monitoring count
        $topLocations = Monitoring::with(['barang.lokasi'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy(function($item) {
                return $item->barang && $item->barang->lokasi ? $item->barang->lokasi->nama_lokasi : 'Unknown';
            })
            ->map(function($group, $key) {
                return [
                    'lokasi' => $key,
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values()
            ->toArray();
            
        return $topLocations;
    }
    
    /**
     * Get recent monitoring activities
     * 
     * @return array
     */
    protected function getRecentActivities()
    {
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();
        
        // Get recent activities
        $recentActivities = Monitoring::with(['barang.lokasi', 'user', 'statusRelation'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->latest('tanggal')
            ->take(10)
            ->get()
            ->map(function($item) {
                return [
                    'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal)->format('d/m/Y H:i:s') : 'N/A',
                    'barang' => $item->barang ? $item->barang->nama_barang : 'N/A',
                    'lokasi' => $item->barang && $item->barang->lokasi ? $item->barang->lokasi->nama_lokasi : 'N/A',
                    'user' => $item->user ? $item->user->nama : 'N/A',
                    'status' => $item->statusRelation ? $item->statusRelation->nama_status : 'N/A',
                ];
            })
            ->toArray();
            
        return $recentActivities;
    }
} 