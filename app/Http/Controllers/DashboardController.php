<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lokasi;
use App\Models\Barang;
use App\Models\Monitoring;
use App\Models\Admin;
use App\Models\Pelaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUser = User::count();
        $totalLokasi = Lokasi::count();
        $totalBarang = Barang::count();
        
        // Get monitoring data for the tables
        $monitoring = Monitoring::with(['barang.lokasi', 'statusRelation', 'user'])
            ->latest('tanggal')
            ->take(10)
            ->get();

        // Get pelaporan data for the tables with proper status mapping
        $pelaporan = Pelaporan::with(['barang.lokasi', 'user', 'statusrelation'])
            ->latest('waktu')
            ->take(10)
            ->get()
            ->map(function($item) {
                // Map status ID to status name
                $statusMap = [
                    '1' => 'Sesuai',
                    '2' => 'Kerusakan',
                    '3' => 'Kehilangan'
                ];
                
                $item->status_display = $item->statusrelation ? 
                    $item->statusrelation->nama_status : 
                    ($statusMap[$item->status] ?? ucfirst($item->status));
                    
                return $item;
            });

        // Get last three months data for chart
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths(2);

        // Create array of the three months
        $dates = [
            $startDate->copy()->startOfMonth(),
            $endDate->copy()->startOfMonth(),
            $endDate->copy()->addMonth()->startOfMonth()
        ];

        $chartData = [
            'labels' => collect($dates)->map(fn($date) => $date->format('M Y'))->toArray(),
            'sesuai' => [],
            'kerusakan' => [],
            'kehilangan' => []
        ];

        // Get statistics for each month
        foreach ($dates as $date) {
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            // For future month, set all values to 0
            if ($monthStart->isFuture()) {
                $chartData['sesuai'][] = 0;
                $chartData['kerusakan'][] = 0;
                $chartData['kehilangan'][] = 0;
                continue;
            }

            // Count monitoring status for 'sesuai'
            $sesuai = Monitoring::whereBetween('tanggal', [$monthStart, $monthEnd])
                ->whereHas('statusRelation', function($query) {
                    $query->where('nama_status', 'sesuai');
                })->count();

            // Count monitoring status for 'kerusakan'
            $kerusakan = Monitoring::whereBetween('tanggal', [$monthStart, $monthEnd])
                ->whereHas('statusRelation', function($query) {
                    $query->where('nama_status', 'kerusakan');
                })->count();

            // Count monitoring status for 'kehilangan'
            $kehilangan = Monitoring::whereBetween('tanggal', [$monthStart, $monthEnd])
                ->whereHas('statusRelation', function($query) {
                    $query->where('nama_status', 'kehilangan');
                })->count();

            $chartData['sesuai'][] = $sesuai;
            $chartData['kerusakan'][] = $kerusakan;
            $chartData['kehilangan'][] = $kehilangan;
        }

        // Get current month's start and end dates for pie chart
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Get counts for kerusakan (current month only)
        $totalKerusakan = Pelaporan::whereBetween('waktu', [$currentMonthStart, $currentMonthEnd])
            ->where('status', '2')  // Hanya ambil status 2 (kerusakan)
            ->count();

        // Get counts for kehilangan (current month only)
        $totalKehilangan = Pelaporan::whereBetween('waktu', [$currentMonthStart, $currentMonthEnd])
            ->where('status', '3')  // Hanya ambil status 3 (kehilangan)
            ->count();

        // Get admin name
        $adminName = Auth::user()->nama ?? 'Admin';

        return view('dashboard', compact(
            'totalUser',
            'totalLokasi',
            'totalBarang',
            'monitoring',
            'pelaporan',
            'adminName',
            'chartData',
            'totalKerusakan',
            'totalKehilangan'
        ));
    }
} 