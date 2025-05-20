<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Barang;

class QRCodeController extends Controller
{
    // Generate QR code for a specific barang
    public function generateQR($id)
    {
        $barang = Barang::findOrFail($id);
        
        // Generate QR dengan URL API publik
        $qrcode = QrCode::size(200)
                    ->format('png')
                    ->generate(url("/api/public/qr/scan/{$id}"));
                    
        return response($qrcode)
              ->header('Content-Type', 'image/png')
              ->header('Content-Disposition', 'inline; filename="qr-barang-'.$id.'.png"');
    }
    
    // Generate QR code for all barang to be displayed on a page
    public function showAllQR()
    {
        $barangs = Barang::with('lokasi')->get();
        return view('qrcode.all', compact('barangs'));
    }
    
    // Download QR code
    public function downloadQR($id)
    {
        $barang = Barang::findOrFail($id);
        
        $qrcode = QrCode::size(200)
                    ->format('png')
                    ->generate(url("/api/public/qr/scan/{$id}"));
        
        return response($qrcode)
              ->header('Content-Type', 'image/png')
              ->header('Content-Disposition', 'attachment; filename="qr-barang-'.$id.'.png"');
    }
    
    // Display the item info when QR is scanned
    public function scanQR($id)
    {
        try {
            $barang = Barang::with('lokasi')->findOrFail($id);
            return view('qrcode.scan', compact('barang'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Barang tidak ditemukan');
        }
    }
    
    // API endpoint untuk scanning QR code - menampilkan semua barang di lokasi yang sama
    public function apiScanQR($id)
    {
        try {
            // Ambil barang yang di-scan
            $barang = Barang::with('lokasi')->findOrFail($id);
            
            // Ambil lokasi ID dari barang tersebut
            $lokasiId = $barang->lokasi->id;
            
            // Ambil semua barang yang berada di lokasi yang sama
            $barangSatuLokasi = Barang::where('lokasi_id', $lokasiId)->get();
            
            return response()->json([
                'success' => true,
                'scanned_barang' => [
                    'id' => $barang->id,
                    'nama_barang' => $barang->nama_barang,
                    'lokasi' => [
                        'id' => $barang->lokasi->id,
                        'nama_lokasi' => $barang->lokasi->nama_lokasi,
                        'latitude' => $barang->lokasi->latitude,
                        'longitude' => $barang->lokasi->longitude
                    ]
                ],
                'barang_satu_lokasi' => $barangSatuLokasi->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama_barang' => $item->nama_barang
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }
    }
}