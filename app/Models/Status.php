<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';
    protected $primaryKey = 'id_status';

    protected $fillable = [
        'nama_status'
    ];

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'status', 'id_status');
    }

    public function pelaporan()
    {
        return $this->hasMany(Pelaporan::class, 'status', 'id_status');
    }
}

