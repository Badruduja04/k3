<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pelaporan extends Model
{
    use HasFactory;
     
    protected $table = 'pelaporan';
    protected $primaryKey = 'id';

    protected $fillable = [ 
        'users',
        'nama_barang',
        'nama_lokasi',
        'status',
        'foto',
        'keterangan',
        'waktu'
    ];

    protected $dates = ['waktu'];
    public $timestamps = true;

    protected $appends = ['foto_url'];

    // Coba mengamankan URL
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return null;
        }
        
        try {
            return asset('storage/' . $this->foto);
        } catch (\Exception $e) {
            \Log::error('Error getting foto URL: ' . $e->getMessage());
            return null;
        }
    }

    // Getter untuk data tanggal yang lebih aman
    public function getFormattedWaktuAttribute()
    {
        if (!$this->waktu) {
            return 'Tidak ada tanggal';
        }
        
        try {
            return Carbon::parse($this->waktu)
                ->locale('id')
                ->translatedFormat('d F Y H:i');
        } catch (\Exception $e) {
            \Log::error('Error formatting waktu: ' . $e->getMessage());
            return 'Format tanggal error';
        }
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'nama_barang', 'id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'nama_lokasi', 'id');
    }

    public function statusrelation()
    {
        return $this->belongsTo(Status::class, 'status', 'id_status');
    }    

    public function user()
    {
        return $this->belongsTo(User::class, 'users', 'id');
    }
}
