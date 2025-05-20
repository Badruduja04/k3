<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    use HasFactory;

    protected $table = 'monitoring';

    protected $fillable = [
        'user_id',
        'id_lokasi',
        'nama_barang',
        'status',
        'keterangan',
        'tanggal',
        'foto',
        'foto_url'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'user_id' => 'integer',
        'nama_barang' => 'integer',
        'status' => 'integer'
    ];

    protected $appends = ['foto_url'];

    protected $hidden = ['foto'];

    public function getFotoUrlAttribute()
    {
        // Check if foto_url field exists and has value in the database
        if (isset($this->attributes['foto_url']) && !empty($this->attributes['foto_url'])) {
            $fotoUrl = $this->attributes['foto_url'];
            
            // For Flutter upload URLs (containing http://10.0.2.2:5000)
            if (strpos($fotoUrl, '10.0.2.2:5000') !== false) {
                // Extract filename from emulator URL
                $pattern = '/\/uploads\/([^\/\s]+\.(jpg|jpeg|png))/i';
                if (preg_match($pattern, $fotoUrl, $matches)) {
                    // Use the extracted filename to create a path to the uploads directory
                    return url('uploads/' . $matches[1]);
                }
            }
            
            // Handle paths like 'uploads/123456.jpg' by ensuring we have a full URL
            if (strpos($fotoUrl, 'uploads/') === 0 || strpos($fotoUrl, '/uploads/') === 0) {
                return url($fotoUrl);
            }
            
            // If it's already a full URL, return it as is
            if (strpos($fotoUrl, 'http://') === 0 || strpos($fotoUrl, 'https://') === 0) {
                return $fotoUrl;
            }
            
            // Otherwise, assume it's a relative path and convert to full URL
            return url($fotoUrl);
        }
        
        // Fallback to database BLOB image if available
        if (!empty($this->foto)) {
            return route('monitoring.image', $this->id);
        }
        
        // No image available
        return null;
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'nama_barang', 'id');
    }

    public function statusRelation()
    {
        return $this->belongsTo(Status::class, 'status', 'id_status');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
} 