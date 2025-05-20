<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $guarded = [];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function kerusakan()
    {
        return $this->hasMany(Kerusakan::class, 'id_barang');
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'nama_barang', 'id');
    }
    
    public function getQRUrl()
    {
        return url('qr/generate/' . $this->id);
    }
    
    public function getScanUrl()
    {
        return url('qr/scan/' . $this->id);
    }

    public function pelaporan()
    {
        return $this->hasMany(Pelaporan::class, 'nama_barang', 'id');
    }

    public function apiScanQR($id)
    {
        \Log::info("API Scan QR dipanggil dengan ID: " . $id);
        
        try {
            // Cari barang langsung dengan query builder untuk debug
            $barang = DB::table('barang')->where('id', $id)->first();
            
            if (!$barang) {
                \Log::error("Barang dengan ID {$id} tidak ditemukan di database");
                return response()->json([
                    'success' => false,
                    'message' => "Barang tidak ditemukan di database"
                ], 404);
            }
            
            \Log::info("Barang ditemukan:", (array)$barang);
            
            // Lanjutkan kode yang sudah ada...
        } catch (\Exception $e) {
            \Log::error("Error saat QR scan: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Barang tidak ditemukan: " . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 404);
        }
    }
} 