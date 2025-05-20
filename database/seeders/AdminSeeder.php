<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Delete existing admin if exists
        DB::table('adminn')->where('nama', 'admin')->delete();

        // Insert new admin with properly hashed password
        DB::table('adminn')->insert([
            'nama' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123')
        ]);
    }
} 