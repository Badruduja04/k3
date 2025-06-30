<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\Monitoring;
use App\Models\Pelaporan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index()
    {
        try {
            // Get accurate counts from each table
            $summaryData = [
                'total_users' => User::count(),
                'total_locations' => Lokasi::count(),
                'total_items' => Barang::count(),
            ];

            // Add diagnostic logging
            \Log::info('Database counts:', [
                'users' => User::count(),
                'locations' => Lokasi::count(), 
                'items' => Barang::count(),
                'monitoring' => Monitoring::count(),
                'pelaporan' => \App\Models\Pelaporan::count()
            ]);

            // Get monthly monitoring statistics for the last 6 months (per status)
            $monthlyStats = collect(range(5, 0))->map(function($i) {
                $date = Carbon::now()->subMonths($i);
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $sesuai = Monitoring::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                    ->whereHas('statusRelation', function($q) {
                        $q->where('nama_status', 'sesuai');
                    })->count();
                $kerusakan = Monitoring::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                    ->whereHas('statusRelation', function($q) {
                        $q->where('nama_status', 'kerusakan');
                    })->count();
                $kehilangan = Monitoring::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                    ->whereHas('statusRelation', function($q) {
                        $q->where('nama_status', 'kehilangan');
                    })->count();

                return [
                    'month' => $date->format('M Y'),
                    'sesuai' => $sesuai,
                    'kerusakan' => $kerusakan,
                    'kehilangan' => $kehilangan
                ];
            })->values()->all();

            // Get location statistics with real data
            $locationStats = Lokasi::select('lokasi.id', 'lokasi.nama_lokasi as name')
                ->selectRaw('COUNT(DISTINCT barang.id) as total_items')
                ->selectRaw('COUNT(DISTINCT CASE WHEN monitoring.status = 1 THEN monitoring.id END) as status_ok')
                ->leftJoin('barang', 'lokasi.id', '=', 'barang.nama_lokasi')
                ->leftJoin('monitoring', 'barang.id', '=', 'monitoring.nama_barang')
                ->groupBy('lokasi.id', 'lokasi.nama_lokasi')
                ->get()
                ->map(function($location) {
                    return [
                        'name' => $location->name,
                        'total_items' => $location->total_items,
                        'status_ok' => $location->status_ok
                    ];
                })->toArray();

            // Get all monitoring data with relationships
            $allMonitoring = Monitoring::select(
                    'monitoring.*',
                    'barang.nama_barang as nama_barang',
                    'lokasi.nama_lokasi as nama_lokasi',
                    'status.nama_status as status_name',
                    'users.nama as user_name'
                )
                ->leftJoin('barang', 'monitoring.nama_barang', '=', 'barang.id')
                ->leftJoin('lokasi', 'barang.nama_lokasi', '=', 'lokasi.id')
                ->leftJoin('status', 'monitoring.status', '=', 'status.id_status')
                ->leftJoin('users', 'monitoring.user_id', '=', 'users.id')
                ->orderBy('monitoring.created_at', 'desc')
                ->get()
                ->map(function($monitoring) {
                    return [
                        'id' => $monitoring->id,
                        'barang' => [
                            'nama_barang' => $monitoring->nama_barang,
                            'lokasi' => [
                                'nama_lokasi' => $monitoring->nama_lokasi
                            ]
                        ],
                        'statusRelation' => [
                            'nama_status' => $monitoring->status_name
                        ],
                        'created_at' => $monitoring->created_at,
                        'user' => [
                            'nama' => $monitoring->user_name
                        ]
                    ];
                });

            // Get all pelaporan data with relationships
            $allPelaporan = Pelaporan::select(
                    'pelaporan.*',
                    'barang.nama_barang as nama_barang',
                    'lokasi.nama_lokasi as nama_lokasi',
                    'status.nama_status as status_name',
                    'users.nama as user_name'
                )
                ->leftJoin('barang', 'pelaporan.nama_barang', '=', 'barang.id')
                ->leftJoin('lokasi', 'barang.nama_lokasi', '=', 'lokasi.id')
                ->leftJoin('status', 'pelaporan.status', '=', 'status.id_status')
                ->leftJoin('users', 'pelaporan.user_id', '=', 'users.id')
                ->orderBy('pelaporan.created_at', 'desc')
                ->get()
                ->map(function($pelaporan) {
                    return [
                        'id' => $pelaporan->id,
                        'tanggal' => $pelaporan->tanggal ? Carbon::parse($pelaporan->tanggal)->format('Y-m-d H:i') : 'N/A',
                        'barang' => [
                            'nama_barang' => $pelaporan->nama_barang,
                            'lokasi' => [
                                'nama_lokasi' => $pelaporan->nama_lokasi
                            ]
                        ],
                        'statusRelation' => [
                            'nama_status' => $pelaporan->status_name
                        ],
                        'user' => [
                            'nama' => $pelaporan->user_name
                        ],
                        'jenis' => 'Pelaporan'
                    ];
                })->toArray();
                
            // Get recent activities from Pelaporan (damage and loss reports)
            $pelaporanActivities = \App\Models\Pelaporan::with(['user', 'barang.lokasi', 'statusrelation'])
                ->latest('waktu')
                ->take(5)
                ->get()
                ->map(function($pelaporan) {
                    return [
                        'tanggal' => $pelaporan->waktu->format('Y-m-d H:i'),
                        'user' => $pelaporan->user ? $pelaporan->user->nama : 'N/A',
                        'lokasi' => $pelaporan->lokasi->nama_lokasi ?? 'N/A',
                        'barang' => $pelaporan->barang ? $pelaporan->barang->nama_barang : 'N/A',
                        'jenis' => 'Pelaporan ' . ucfirst($pelaporan->statusrelation->nama_status ?? ''),
                        'status' => $pelaporan->statusrelation->nama_status ?? 'N/A'
                    ];
                })->toArray();

            // Get recent activities from Pelaporan with better null handling
            $pelaporanActivities = \App\Models\Pelaporan::with(['user', 'barang.lokasi', 'statusrelation'])
                ->latest('waktu')
                ->take(5)
                ->get()
                ->map(function($pelaporan) {
                    $tanggal = $pelaporan->waktu ? $pelaporan->waktu->format('Y-m-d H:i') : 'N/A';
                    $user = ($pelaporan->user && isset($pelaporan->user->nama)) ? $pelaporan->user->nama : 'N/A';
                    $barang = ($pelaporan->barang && isset($pelaporan->barang->nama_barang)) ? $pelaporan->barang->nama_barang : 'N/A';
                    $lokasi = ($pelaporan->lokasi && isset($pelaporan->lokasi->nama_lokasi)) ? $pelaporan->lokasi->nama_lokasi : 'N/A';
                    $status = ($pelaporan->statusrelation && isset($pelaporan->statusrelation->nama_status)) 
                        ? $pelaporan->statusrelation->nama_status 
                        : 'N/A';
                    $jenis = 'Pelaporan ' . ($status !== 'N/A' ? ucfirst($status) : '');
                    
                    return [
                        'tanggal' => $tanggal,
                        'user' => $user,
                        'lokasi' => $lokasi,
                        'barang' => $barang,
                        'jenis' => $jenis,
                        'status' => $status
                    ];
                })->toArray();
                
            // Combine both collections and sort by date (newest first)
            $recentActivities = collect(array_merge($monitoringActivities, $pelaporanActivities))
                ->sortByDesc('tanggal')
                ->take(10)
                ->values()
                ->toArray();

            // Get total monitoring by status
            $statusSesuai = Monitoring::whereHas('statusRelation', function($q) {
                $q->where('nama_status', 'sesuai');
            })->count();
            $statusKerusakan = Monitoring::whereHas('statusRelation', function($q) {
                $q->where('nama_status', 'kerusakan');
            })->count();
            $statusKehilangan = Monitoring::whereHas('statusRelation', function($q) {
                $q->where('nama_status', 'kehilangan');
            })->count();
            $monitoringStatusCounts = [
                'sesuai' => $statusSesuai,
                'kerusakan' => $statusKerusakan,
                'kehilangan' => $statusKehilangan,
            ];

            // Ambil data monitoring dan pelaporan tanpa relasi untuk debug
            $allMonitoring = Monitoring::orderBy('tanggal', 'desc')->get();
            $allPelaporan = Pelaporan::orderBy('waktu', 'desc')->get();
            dd($allMonitoring->take(5));

            return view('report', compact('summaryData', 'monthlyStats', 'locationStats', 'recentActivities', 'monitoringStatusCounts', 'allMonitoring', 'allPelaporan'));
        } catch (\Exception $e) {
            \Log::error('Report Error: ' . $e->getMessage());
            
            // Return view with empty data if error occurs
            return view('report', [
                'summaryData' => [
                    'total_users' => User::count(),
                    'total_locations' => Lokasi::count(),
                    'total_items' => Barang::count(),
                ],
                'monthlyStats' => [],
                'locationStats' => [],
                'recentActivities' => [],
                'monitoringStatusCounts' => [],
                'allMonitoring' => [],
                'allPelaporan' => []
            ]);
        }
    }

    public function exportExcel()
    {
        try {
            // Get the ZIP file
            $reportExport = new \App\Exports\ReportExport();
            $zipFile = $reportExport->createZip();
            
            // Return the file as a download
            return response()->download($zipFile, 'laporan-data-' . date('Y-m-d') . '.zip', [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Excel Export Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengexport data ke Excel. Silakan coba lagi.');
        }
    }
    
    /**
     * Export monthly report for a specific month
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportMonthlyReport(Request $request)
    {
        try {
            // Get month and year from request or use current month/year
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));
            
            // Generate the monthly report
            $reportExport = new \App\Exports\ReportExport();
            $filePath = $reportExport->createMonthlyReport($month, $year);
            
            // Format month name for the filename
            $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->locale('id')->format('F-Y');
            
            // Return the file as a download
            return response()->download($filePath, 'laporan-bulanan-' . $monthName . '.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Monthly Report Export Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengexport laporan bulanan. Silakan coba lagi.');
        }
    }

    /**
     * Test method to directly check data
     */
    public function testData()
    {
        try {
            // Get monitoring data directly
            $monitoring = Monitoring::with(['barang.lokasi', 'statusRelation', 'user'])
                ->latest('tanggal')
                ->take(5)
                ->get();
                
            // Get pelaporan data directly
            $pelaporan = \App\Models\Pelaporan::with(['user', 'barang.lokasi', 'statusrelation'])
                ->latest('waktu')
                ->take(5)
                ->get();
                
            // Output raw data for debugging
            return response()->json([
                'success' => true,
                'monitoring_count' => Monitoring::count(),
                'pelaporan_count' => \App\Models\Pelaporan::count(),
                'monitoring_data' => $monitoring,
                'pelaporan_data' => $pelaporan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Create test data if none exists
     */
    public function createTestData()
    {
        try {
            $monitoringCount = Monitoring::count();
            $pelaporanCount = \App\Models\Pelaporan::count();
            $userCount = User::count();
            $lokasiCount = Lokasi::count();
            $barangCount = Barang::count();
            
            $created = [
                'monitoring' => 0,
                'pelaporan' => 0,
                'users' => 0,
                'lokasi' => 0,
                'barang' => 0
            ];
            
            // Create a test user if none exists
            if ($userCount == 0) {
                User::create([
                    'nama' => 'Test User',
                    'email' => 'test@example.com',
                    'password' => bcrypt('password'),
                    'NUP' => '12345',
                    'departement' => 'IT',
                    'sub_departement' => 'Development',
                    'status' => 'aktif'
                ]);
                $created['users']++;
            }
            
            // Create a test location if none exists
            if ($lokasiCount == 0) {
                Lokasi::create([
                    'nama_lokasi' => 'Test Location',
                    'latitude' => 0,
                    'longitude' => 0
                ]);
                $created['lokasi']++;
            }
            
            // Create a test item if none exists
            if ($barangCount == 0) {
                $lokasi = Lokasi::first();
                Barang::create([
                    'nama_barang' => 'Test Item',
                    'id_lokasi' => $lokasi->id
                ]);
                $created['barang']++;
            }
            
            // Create test monitoring data if none exists
            if ($monitoringCount == 0) {
                $user = User::first();
                $barang = Barang::first();
                $lokasi = Lokasi::first();
                
                // Create a status if it doesn't exist
                $sesuaiStatus = \App\Models\Status::firstOrCreate(
                    ['nama_status' => 'sesuai'],
                    ['nama_status' => 'sesuai']
                );
                
                Monitoring::create([
                    'user_id' => $user->id,
                    'id_lokasi' => $lokasi->id,
                    'nama_barang' => $barang->id,
                    'status' => $sesuaiStatus->id_status,
                    'keterangan' => 'Test monitoring data',
                    'tanggal' => now()
                ]);
                $created['monitoring']++;
            }
            
            // Create test pelaporan data if none exists
            if ($pelaporanCount == 0) {
                $user = User::first();
                $barang = Barang::first();
                $lokasi = Lokasi::first();
                
                // Create status records if they don't exist
                $kerusakanStatus = \App\Models\Status::firstOrCreate(
                    ['nama_status' => 'kerusakan'],
                    ['nama_status' => 'kerusakan']
                );
                
                \App\Models\Pelaporan::create([
                    'users' => $user->id,
                    'nama_barang' => $barang->id,
                    'nama_lokasi' => $lokasi->id,
                    'status' => $kerusakanStatus->id_status,
                    'keterangan' => 'Test pelaporan data',
                    'waktu' => now()
                ]);
                $created['pelaporan']++;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Test data created successfully',
                'created' => $created,
                'monitoring_count' => Monitoring::count(),
                'pelaporan_count' => \App\Models\Pelaporan::count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}