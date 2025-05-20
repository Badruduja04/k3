<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\Pelaporan;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class GeneralExport
{
    public function __construct($dataTypes = [])
    {
        $this->dataTypes = $dataTypes;
    }

    public function export($filePath)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheetIndex = 0;
        foreach ($this->dataTypes as $type) {
            $sheet = $sheetIndex === 0
                ? $spreadsheet->getActiveSheet()
                : $spreadsheet->createSheet($sheetIndex);
            
            switch ($type) {
                case 'users':
                    $sheet->setTitle('Users');
                    $headers = ['ID', 'Nama', 'Email', 'NUP', 'Departement', 'Sub Departement', 'Status'];
                    $data = \App\Models\User::all(['id', 'nama', 'email', 'NUP', 'departement', 'sub_departement', 'status']);
                    break;
                case 'barang':
                    $sheet->setTitle('Barang');
                    $headers = ['ID', 'Nama Barang', 'ID Lokasi'];
                    $data = \App\Models\Barang::all(['id', 'nama_barang', 'id_lokasi']);
                    break;
                case 'lokasi':
                    $sheet->setTitle('Lokasi');
                    $headers = ['ID', 'Nama Lokasi', 'Latitude', 'Longitude'];
                    $data = \App\Models\Lokasi::all(['id', 'nama_lokasi', 'latitude', 'longitude']);
                    break;
                case 'monitoring':
                    $sheet->setTitle('Monitoring');
                    $headers = ['ID', 'Tanggal', 'User', 'Nama Barang', 'Lokasi', 'Status', 'Keterangan'];
                    $data = \App\Models\Monitoring::with(['user', 'barang', 'lokasi', 'statusRelation'])->get();
                    break;
                case 'pelaporan_kerusakan':
                    $sheet->setTitle('Pelaporan Kerusakan');
                    $headers = ['ID', 'User', 'Nama Barang', 'Nama Lokasi', 'Status', 'Keterangan', 'Waktu'];
                    $data = \App\Models\Pelaporan::whereHas('statusrelation', function ($query) {
                        $query->where('nama_status', 'kerusakan');
                    })->with(['user', 'barang', 'lokasi', 'statusrelation'])->get();
                    break;
                case 'pelaporan_kehilangan':
                    $sheet->setTitle('Pelaporan Kehilangan');
                    $headers = ['ID', 'User', 'Nama Barang', 'Nama Lokasi', 'Status', 'Keterangan', 'Waktu'];
                    $data = \App\Models\Pelaporan::whereHas('statusrelation', function ($query) {
                        $query->where('nama_status', 'kehilangan');
                    })->with(['user', 'barang', 'lokasi', 'statusrelation'])->get();
                    break;
                default:
                    $headers = [];
                    $data = collect();
            }
            $sheet->fromArray($headers, null, 'A1');
            $row = 2;
            foreach ($data as $item) {
                switch ($type) {
                    case 'users':
                        $sheet->fromArray([
                            $item->id, $item->nama, $item->email, $item->NUP, $item->departement, $item->sub_departement, $item->status
                        ], null, 'A'.$row);
                        break;
                    case 'barang':
                        $sheet->fromArray([
                            $item->id, $item->nama_barang, $item->id_lokasi
                        ], null, 'A'.$row);
                        break;
                    case 'lokasi':
                        $sheet->fromArray([
                            $item->id, $item->nama_lokasi, $item->latitude, $item->longitude
                        ], null, 'A'.$row);
                        break;
                    case 'monitoring':
                        $sheet->fromArray([
                            $item->id,
                            $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d F Y H:i') : '',
                            $item->user ? $item->user->nama : '',
                            $item->barang ? $item->barang->nama_barang : '',
                            $item->lokasi ? $item->lokasi->nama_lokasi : '',
                            $item->statusRelation ? $item->statusRelation->nama_status : '',
                            $item->keterangan
                        ], null, 'A'.$row);
                        break;
                    case 'pelaporan_kerusakan':
                    case 'pelaporan_kehilangan':
                        $sheet->fromArray([
                            $item->id,
                            $item->user ? $item->user->nama : '',
                            $item->barang ? $item->barang->nama_barang : '',
                            $item->lokasi ? $item->lokasi->nama_lokasi : '',
                            $item->statusrelation ? $item->statusrelation->nama_status : '',
                            $item->keterangan,
                            $item->waktu ? \Carbon\Carbon::parse($item->waktu)->locale('id')->translatedFormat('d F Y H:i') : ''
                        ], null, 'A'.$row);
                        break;
                }
                $row++;
            }
            $sheetIndex++;
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);
    }
} 