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
        // Check if foto_url field exists and has value
        if (!empty($this->attributes['foto_url'])) {
            return 'uploads/' . basename($this->attributes['foto_url']);
        }

        // If foto_url is empty but foto field exists
        if (!empty($this->attributes['foto'])) {
            // Assuming foto field contains filename
            return 'uploads/' . $this->attributes['foto'];
        }

        // Return default image if no foto is available
        return 'images/no-image.jpg';
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