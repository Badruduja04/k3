<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelaporan;
use App\Models\Barang;
use App\Models\Status;
use App\Models\Lokasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PelaporanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Pelaporan::with(['user', 'lokasi', 'barang', 'statusrelation']);
            
            // Process date filtering
            if ($request->has('preset_period') && $request->preset_period != '') {
                $today = Carbon::today();
                
                switch ($request->preset_period) {
                    case 'today':
                        $query->whereDate('waktu', $today);
                        break;
                        
                    case 'yesterday':
                        $query->whereDate('waktu', $today->copy()->subDay());
                        break;
                        
                    case 'week':
                        $query->whereBetween('waktu', [
                            $today->copy()->subDays(7),
                            $today->copy()->endOfDay()
                        ]);
                        break;
                        
                    case 'month':
                        $query->whereBetween('waktu', [
                            $today->copy()->subDays(30),
                            $today->copy()->endOfDay()
                        ]);
                        break;
                        
                    case 'custom':
                        if ($request->filled('start_date') && $request->filled('end_date')) {
                            try {
                                $startDate = Carbon::parse($request->start_date)->startOfDay();
                                $endDate = Carbon::parse($request->end_date)->endOfDay();
                                
                                // Validate if end date is greater than or equal to start date
                                if ($endDate->lt($startDate)) {
                                    return back()->with('error', 'Tanggal akhir harus setelah tanggal awal')->withInput();
                                }
                                
                                $query->whereBetween('waktu', [$startDate, $endDate]);
                            } catch (\Exception $e) {
                                return back()->with('error', 'Format tanggal tidak valid')->withInput();
                            }
                        } else {
                            if ($request->preset_period == 'custom') {
                                return back()->with('error', 'Silakan pilih tanggal awal dan akhir untuk filter kustom')->withInput();
                            }
                        }
                        break;
                }
            }
            
            $pelaporanItems = $query->orderBy('waktu', 'desc')->get();
            
            // Group pelaporan items by location
            $pelaporanByLocation = [];
            
            foreach ($pelaporanItems as $item) {
                $locationId = $item->nama_lokasi;
                $locationName = $item->lokasi ? $item->lokasi->nama_lokasi : 'Lokasi Tidak Diketahui';
                
                if (!isset($pelaporanByLocation[$locationId])) {
                    $pelaporanByLocation[$locationId] = [
                        'nama_lokasi' => $locationName,
                        'items' => []
                    ];
                }
                
                $pelaporanByLocation[$locationId]['items'][] = $item;
            }
            
            return view('pelaporan', compact('pelaporanByLocation'));
        } catch (\Exception $e) {
            Log::error('Error dalam PelaporanController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data');
        }
    }
    
    public function showLocation($locationId)
    {
        try {
            // Get pelaporan items with their relations for the specified location
            $pelaporanItems = Pelaporan::with(['user', 'lokasi', 'barang', 'statusrelation'])
                ->where('nama_lokasi', $locationId)
                ->orderBy('waktu', 'desc')
                ->get();
                
            // If no items found, return with an error
            if ($pelaporanItems->isEmpty()) {
                return back()->with('error', 'Tidak ada data pelaporan untuk lokasi ini');
            }
            
            // Get location name from the first item
            $locationName = $pelaporanItems->first()->lokasi ? $pelaporanItems->first()->lokasi->nama_lokasi : 'Lokasi Tidak Diketahui';
            
            // Create the location data array
            $locationData = [
                'nama_lokasi' => $locationName,
                'items' => $pelaporanItems
            ];
            
            return view('pelaporan_location', compact('locationData'));
        } catch (\Exception $e) {
            Log::error('Error dalam PelaporanController@showLocation: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data lokasi');
        }
    }

    public function show($id)
    {
        try {
            // Dapatkan data dengan eager loading
            $pelaporan = Pelaporan::with(['user', 'barang.lokasi', 'statusrelation'])
                ->findOrFail($id);
            
            // Ambil data relasi dengan pengecekan null-safe
            $userName = $pelaporan->user->nama ?? 'N/A';
            $barangName = $pelaporan->barang->nama_barang ?? 'N/A';
            $lokasiName = $pelaporan->barang->lokasi->nama_lokasi ?? 'N/A';
            $nama_status = $pelaporan->statusrelation->nama_status ?? 'Error';
            
            // Format tanggal secara manual
            $tanggal = 'Tidak ada tanggal';
            if ($pelaporan->waktu) {
                try {
                    $tanggal = \Carbon\Carbon::parse($pelaporan->waktu)
                        ->locale('id')
                        ->translatedFormat('d F Y H:i');
                } catch (\Exception $e) {
                    Log::error('Error format tanggal: ' . $e->getMessage());
                }
            }
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $pelaporan->id,
                    'user_id' => $pelaporan->users,
                    'user_name' => $userName,
                    'barang' => $barangName,
                    'lokasi' => $lokasiName,
                    'status' => $nama_status,
                    'tanggal' => $tanggal,
                    'keterangan' => $pelaporan->keterangan ?? 'Tidak ada keterangan',
                    'file' => $pelaporan->foto ? true : false,
                    'file_url' => $pelaporan->foto ? route('pelaporan.image', $pelaporan->id) : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error dalam PelaporanController@show: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detail($id)
    {
        try {
            $pelaporan = Pelaporan::with(['user', 'barang.lokasi'])
                ->findOrFail($id);

            $data = [
                'id' => $pelaporan->id,
                'lokasi' => $pelaporan->barang->lokasi->nama_lokasi ?? 'N/A',
                'nama_barang' => $pelaporan->barang->nama_barang ?? 'N/A',
                'status' => ucfirst($pelaporan->status),
                'waktu' => $pelaporan->waktu ? Carbon::parse($pelaporan->waktu)->locale('id')->translatedFormat('d F Y H:i') : 'N/A',
                'user' => $pelaporan->user->nama ?? 'N/A',
                'keterangan' => $pelaporan->keterangan ?? 'N/A',
                'foto_url' => $pelaporan->foto ? url('/pelaporan/image/' . $pelaporan->id) : null
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Error in PelaporanController@detail: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data'], 500);
        }
    }

    public function detailPage($id)
    {
        try {
            // Log request detail untuk debugging
            Log::info('Permintaan detail page pelaporan', ['id' => $id]);
            
            // Dapatkan data dasar
            $pelaporan = Pelaporan::findOrFail($id);
            
            // Ambil data relasi secara manual untuk menghindari error
            $userName = 'N/A';
            $barangName = 'N/A';
            $lokasiName = 'N/A';
            $nama_status = 'Error';
            
            if ($pelaporan->users) {
                $user = User::find($pelaporan->users);
                if ($user) {
                    $userName = $user->nama;
                }
            }
            
            if ($pelaporan->nama_barang) {
                $barang = Barang::find($pelaporan->nama_barang);
                if ($barang) {
                    $barangName = $barang->nama_barang;
                    
                    if (isset($barang->lokasi) && $barang->lokasi) {
                        $lokasiName = $barang->lokasi->nama_lokasi;
                    }
                }
            }
            
            if ($pelaporan->status) {
                $status = Status::where('id_status', $pelaporan->status)->first();
                if ($status) {
                    $nama_status = $status->nama_status;
                }
            }
            
            // Format tanggal secara manual
            $tanggal = 'Tidak ada tanggal';
            if ($pelaporan->waktu) {
                try {
                    $tanggal = \Carbon\Carbon::parse($pelaporan->waktu)
                        ->locale('id')
                        ->translatedFormat('d F Y H:i');
                } catch (\Exception $e) {
                    Log::error('Error format tanggal: ' . $e->getMessage());
                }
            }
            
            // Struktur data untuk view
            $data = (object)[
                'id' => $pelaporan->id,
                'user_id' => $pelaporan->users,
                'user_name' => $userName,
                'barang' => $barangName,
                'lokasi' => $lokasiName,
                'status' => $nama_status,
                'tanggal' => $tanggal,
                'keterangan' => $pelaporan->keterangan ?? 'Tidak ada keterangan',
                'file' => $pelaporan->foto ? true : false,
                'file_url' => $pelaporan->foto ? route('pelaporan.image', $pelaporan->id) : null
            ];
            
            return view('pelaporan_detail', compact('data'));
        } catch (\Exception $e) {
            Log::error('Error dalam PelaporanController@detailPage: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage());
        }
    }

    // Method untuk menampilkan gambar
    public function showImage($id)
    {
        try {
            $pelaporan = Pelaporan::findOrFail($id);
            if (!$pelaporan->foto) {
                abort(404, 'Image not found');
            }
            
            // Cek jika foto disimpan sebagai path file
            if (Storage::disk('public')->exists($pelaporan->foto)) {
                return response()->file(Storage::disk('public')->path($pelaporan->foto));
            }
            
            // Jika foto disimpan sebagai binary dalam database
            return response($pelaporan->foto)
                ->header('Content-Type', 'image/jpeg');
        } catch (\Exception $e) {
            Log::error('Error dalam PelaporanController@showImage: ' . $e->getMessage());
            abort(500, 'Error displaying image');
        }
    }
}