<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelaporan;

class ExportController extends Controller
{
    public function generalExport(Request $request)
    {
        $dataTypes = $request->input('data', []);
        if (empty($dataTypes)) {
            return back()->with('error', 'Pilih minimal satu tipe data untuk diexport!');
        }
        
        $export = new \App\Exports\GeneralExport($dataTypes);
        
        // Buat nama file yang sesuai
        $fileName = $this->getFileName($dataTypes);
        $filePath = storage_path('app/exports/' . $fileName);
        
        $export->export($filePath);
        
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
    
    /**
     * Mendapatkan nama file sesuai tipe data yang dipilih
     * 
     * @param array $dataTypes
     * @return string
     */
    private function getFileName($dataTypes)
    {
        if (count($dataTypes) === 1) {
            // Jika hanya satu tipe data, gunakan nama tipe data tersebut
            $type = $dataTypes[0];
            $labels = [
                'users' => 'Data Users',
                'barang' => 'Data Barang',
                'lokasi' => 'Data Lokasi',
                'monitoring' => 'Data Monitoring',
                'pelaporan_kerusakan' => 'Data Pelaporan Kerusakan',
                'pelaporan_kehilangan' => 'Data Pelaporan Kehilangan'
            ];
            
            $name = $labels[$type] ?? 'Data ' . ucfirst($type);
            return $name . '.xlsx';
        } else {
            // Jika lebih dari satu tipe data, gunakan nama general
            return 'Data General.xlsx';
        }
    }
} 