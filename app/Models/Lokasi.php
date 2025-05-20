<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';
    
    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude'
    ];

    // Disable timestamps if your table doesn't have them
    public $timestamps = false;

    // Set the primary key if it's different from 'id'
    protected $primaryKey = 'id';

    // Set the key type if it's not incremental
    protected $keyType = 'int';

    protected $attributes = [
        'latitude' => 0,
        'longitude' => 0
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_lokasi');
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class);
    }

    public function pelaporan()
    {
        return $this->hasMany(Pelaporan::class, 'nama_lokasi', 'id');
    }
} 