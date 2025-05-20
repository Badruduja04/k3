<?php

namespace App\Exports;

use ZipArchive;
use Carbon\Carbon;

class ReportExport
{
    /**
     * Create a ZIP file containing all Excel exports
     * 
     * @return string Path to the created ZIP file
     */
    public function createZip()
    {
        $zipFile = storage_path('app/exports/report-' . date('Y-m-d-His') . '.zip');
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($zipFile))) {
            mkdir(dirname($zipFile), 0755, true);
        }
        
        // Create temporary directory for Excel files
        $tmpDir = storage_path('app/exports/tmp');
        if (!file_exists($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        
        // Generate Excel files
        $barangFile = $tmpDir . '/barang.xlsx';
        $userFile = $tmpDir . '/users.xlsx';
        $lokasiFile = $tmpDir . '/lokasi.xlsx';
        $monitoringFile = $tmpDir . '/monitoring.xlsx';
        $damageFile = $tmpDir . '/laporan-kerusakan.xlsx';
        $lossFile = $tmpDir . '/laporan-kehilangan.xlsx';
        
        // Create the Excel files
        $barangExport = new BarangExport();
        $barangExport->export($barangFile);
        
        $userExport = new UserExport();
        $userExport->export($userFile);
        
        $lokasiExport = new LokasiExport();
        $lokasiExport->export($lokasiFile);
        
        $monitoringExport = new MonitoringExport();
        $monitoringExport->export($monitoringFile);

        // Create damage report
        $damageExport = new DamageReportExport();
        $damageExport->export($damageFile);

        // Create loss report
        $lossExport = new LossReportExport();
        $lossExport->export($lossFile);
        
        // Create ZIP file
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Cannot create ZIP file: " . $zipFile);
        }
        
        // Add files to ZIP
        $zip->addFile($barangFile, 'barang.xlsx');
        $zip->addFile($userFile, 'users.xlsx');
        $zip->addFile($lokasiFile, 'lokasi.xlsx');
        $zip->addFile($monitoringFile, 'monitoring.xlsx');
        $zip->addFile($damageFile, 'laporan-kerusakan.xlsx');
        $zip->addFile($lossFile, 'laporan-kehilangan.xlsx');
        
        $zip->close();
        
        // Clean up temporary files
        @unlink($barangFile);
        @unlink($userFile);
        @unlink($lokasiFile);
        @unlink($monitoringFile);
        @unlink($damageFile);
        @unlink($lossFile);
        @rmdir($tmpDir);
        
        return $zipFile;
    }
    
    /**
     * Create monthly report for a specific month/year
     * 
     * @param int|null $month Month (1-12)
     * @param int|null $year Year
     * @return string Path to the generated Excel file
     */
    public function createMonthlyReport($month = null, $year = null)
    {
        // Set default values if not provided
        $month = $month ?: date('m');
        $year = $year ?: date('Y');
        
        // Format month name for the filename
        $monthName = Carbon::createFromDate($year, $month, 1)->locale('id')->format('F-Y');
        
        // Create file path
        $filePath = storage_path('app/exports/laporan-bulanan-' . $monthName . '.xlsx');
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        // Create the export and generate the file
        $export = new MonthlyReportExport($month, $year);
        $export->export($filePath);
        
        return $filePath;
    }

    /**
     * Create damage report for a specific month/year
     * 
     * @param int|null $month Month (1-12)
     * @param int|null $year Year
     * @return string Path to the generated Excel file
     */
    public function createDamageReport($month = null, $year = null)
    {
        // Set default values if not provided
        $month = $month ?: date('m');
        $year = $year ?: date('Y');
        
        // Format month name for the filename
        $monthName = Carbon::createFromDate($year, $month, 1)->locale('id')->format('F-Y');
        
        // Create file path
        $filePath = storage_path('app/exports/laporan-kerusakan-' . $monthName . '.xlsx');
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        // Create the export and generate the file
        $export = new DamageReportExport($month, $year);
        $export->export($filePath);
        
        return $filePath;
    }

    /**
     * Create loss report for a specific month/year
     * 
     * @param int|null $month Month (1-12)
     * @param int|null $year Year
     * @return string Path to the generated Excel file
     */
    public function createLossReport($month = null, $year = null)
    {
        // Set default values if not provided
        $month = $month ?: date('m');
        $year = $year ?: date('Y');
        
        // Format month name for the filename
        $monthName = Carbon::createFromDate($year, $month, 1)->locale('id')->format('F-Y');
        
        // Create file path
        $filePath = storage_path('app/exports/laporan-kehilangan-' . $monthName . '.xlsx');
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        // Create the export and generate the file
        $export = new LossReportExport($month, $year);
        $export->export($filePath);
        
        return $filePath;
    }
} 