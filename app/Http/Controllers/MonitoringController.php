<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monitoring;
use App\Models\Barang;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function index()
    {
        try {
            // Get all locations directly from the database
            $locations = DB::table('lokasi')->get();
            
            // Initialize the monitoring by location array with all locations
            $monitoringByLocation = [];
            foreach ($locations as $location) {
                $monitoringByLocation[$location->id] = [
                    'nama_lokasi' => $location->nama_lokasi,
                    'items' => []
                ];
            }
            
            // Get monitoring items with their relations
            $monitoringItems = Monitoring::with(['barang.lokasi', 'statusRelation', 'user'])->get();
            
            // Group monitoring items by location
            foreach ($monitoringItems as $item) {
                if ($item->barang && $item->barang->lokasi) {
                    $locationId = $item->barang->lokasi->id;
                    if (isset($monitoringByLocation[$locationId])) {
                        $monitoringByLocation[$locationId]['items'][] = $item;
                    }
                }
            }
            
            return view('monitoring', compact('monitoringByLocation'));
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data');
        }
    }

    public function show($id)
    {
        try {
            $monitoring = Monitoring::with(['barang.lokasi', 'statusRelation', 'user'])->findOrFail($id);
            
            $data = [
                'id' => $monitoring->id,
                'barang' => $monitoring->barang ? $monitoring->barang->nama_barang : 'N/A',
                'status' => $monitoring->statusRelation ? $monitoring->statusRelation->nama_status : 'N/A',
                'keterangan' => $monitoring->keterangan ?? 'N/A',
                'tanggal' => $monitoring->tanggal ? $monitoring->tanggal->locale('id')->translatedFormat('d F Y H:i') : 'N/A',
                'user_id' => $monitoring->user ? $monitoring->user->NUP : 'N/A',
                'user_name' => $monitoring->user ? $monitoring->user->nama : 'N/A'
            ];

            // Handle binary file data
            if ($monitoring->foto !== null) {
                $data['foto'] = [
                    'content' => base64_encode($monitoring->foto),
                    'mime_type' => 'image/jpeg'  // Default to JPEG since we're storing images
                ];
            } else {
                $data['foto'] = null;
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@show: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data'
            ], 500);
        }
    }

    public function detail($id)
    {
        try {
            $monitoring = Monitoring::with(['barang.lokasi', 'statusRelation', 'user'])
                ->findOrFail($id);

            return response()->json([
                'keterangan' => $monitoring->keterangan ?? 'Tidak ada keterangan',
                'foto_url' => $monitoring->foto_url,
                'lokasi' => $monitoring->lokasi->nama_lokasi ?? 'N/A',
                'nama_barang' => $monitoring->barang->nama_barang ?? 'N/A',
                'status' => $monitoring->statusRelation->nama_status ?? 'N/A',
                'tanggal' => $monitoring->tanggal ? $monitoring->tanggal->locale('id')->translatedFormat('d F Y H:i') : 'N/A',
                'user' => $monitoring->user->nama ?? 'N/A',
                'nup' => $monitoring->nup ?? 'N/A'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_barang' => 'required|exists:barang,id',
                'status' => 'required|exists:status,id_status',
                'keterangan' => 'nullable|string',
                'tanggal' => 'required|date',
                'foto' => 'nullable|image|max:2048',
                'foto_url' => 'nullable|string'
            ]);

            // Handle mobile uploads format
            if (!empty($validated['foto_url']) && !str_contains($validated['foto_url'], 'uploads/')) {
                // Check if it looks like a timestamp filename (all digits)
                if (preg_match('/^\d+\.jpg$/', basename($validated['foto_url']))) {
                    $validated['foto_url'] = 'uploads/' . basename($validated['foto_url']);
                }
            }

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $validated['foto'] = file_get_contents($foto->getRealPath());
            }

            // Add user_id to the validated data
            $validated['user_id'] = auth()->id();

            $monitoring = Monitoring::create($validated);

            return redirect()->back()->with('success', 'Data monitoring berhasil ditambahkan');
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $monitoring = Monitoring::findOrFail($id);

            $validated = $request->validate([
                'nama_barang' => 'required|exists:barang,id',
                'status' => 'required|exists:status,id_status',
                'keterangan' => 'nullable|string',
                'tanggal' => 'required|date',
                'foto' => 'nullable|image|max:2048',
                'foto_url' => 'nullable|string'
            ]);

            // Handle mobile uploads format
            if (!empty($validated['foto_url']) && !str_contains($validated['foto_url'], 'uploads/')) {
                // Check if it looks like a timestamp filename (all digits)
                if (preg_match('/^\d+\.jpg$/', basename($validated['foto_url']))) {
                    $validated['foto_url'] = 'uploads/' . basename($validated['foto_url']);
                }
            }

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $validated['foto'] = file_get_contents($foto->getRealPath());
            }

            $monitoring->update($validated);

            return redirect()->back()->with('success', 'Data monitoring berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data');
        }
    }

    public function destroy($id)
    {
        try {
            $monitoring = Monitoring::findOrFail($id);
            
            // Delete file if exists
            if ($monitoring->foto) {
                Storage::disk('public')->delete($monitoring->foto);
            }
            
            $monitoring->delete();

            return redirect()->back()->with('success', 'Data monitoring berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }

    // Method untuk menampilkan gambar
    public function showImage($id)
    {
        try {
            \Log::info("showImage method called for monitoring ID: $id");
            $monitoring = DB::select('SELECT foto, foto_url FROM monitoring WHERE id = ?', [$id]);
            if (empty($monitoring)) {
                return response()->file(public_path('images/no-image.jpg'));
            }
            $fotoData = $monitoring[0]->foto;
            $fotoUrl = isset($monitoring[0]->foto_url) ? str_replace('\\', '/', $monitoring[0]->foto_url) : null;
            if (!empty($fotoData) && strlen($fotoData) >= 10) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($fotoData) ?: 'image/jpeg';
                return response($fotoData)
                    ->header('Content-Type', $mimeType)
                    ->header('Content-Length', strlen($fotoData))
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            }
            // Jika tidak ada BLOB, cek file di uploads
            if (!empty($fotoUrl)) {
                $filename = basename($fotoUrl);
                $directPath = public_path('uploads/' . $filename);
                if (file_exists($directPath)) {
                    return response()->file($directPath);
                }
                $localPath = public_path(ltrim($fotoUrl, '/'));
                if (file_exists($localPath)) {
                    return response()->file($localPath);
                }
            }
            return response()->file(public_path('images/no-image.jpg'));
        } catch (\Exception $e) {
            return response()->file(public_path('images/no-image.jpg'));
        }
    }

    // Add a method to get raw image data for base64 encoding in JS
    public function getFile($id)
    {
        try {
            \Log::info("getFile method called for monitoring ID: $id");
            
            // Always go directly to the database for the most reliable data
            $rawData = DB::table('monitoring')->where('id', $id)->first();
            if (!$rawData) {
                \Log::warning("Monitoring record not found for ID: $id");
                return $this->returnPlaceholderImage('Data monitoring tidak ditemukan');
            }
            
            // First check if there's foto data (BLOB) in the database
            if (!empty($rawData->foto)) {
                \Log::info("Found BLOB image data in database, size: " . strlen($rawData->foto));
                $fotoData = $rawData->foto;
                if (strlen($fotoData) >= 10) {
                    $base64Data = base64_encode($fotoData);
                    \Log::info("Returning BLOB image as base64, length: " . strlen($base64Data));
                    return response()->json(['data' => $base64Data]);
        }
            }
            
            // Check if there's a foto_url in the database
            if (!empty($rawData->foto_url)) {
                $fotoUrl = str_replace('\\', '/', $rawData->foto_url); // Normalisasi path
                $filename = basename($fotoUrl);
                // 1. Cek langsung di uploads folder
                $directPath = public_path('uploads/' . $filename);
                if (file_exists($directPath) && is_readable($directPath)) {
                    $fileData = file_get_contents($directPath);
                    return response()->json(['data' => base64_encode($fileData)]);
                }
                // 2. Cek path yang sudah dinormalisasi
                $localPath = public_path(ltrim($fotoUrl, '/'));
                if (file_exists($localPath) && is_readable($localPath)) {
                    $fileData = file_get_contents($localPath);
                    return response()->json(['data' => base64_encode($fileData)]);
                }
            }
            
            // Last resort - check if we can find any image in the uploads folder with the right format
            $this->searchUploadsFolder($id);
            
            // If we got here, neither the BLOB nor file was successfully found
            \Log::warning("No valid image found for monitoring ID: $id");
            return $this->returnPlaceholderImage('Gambar tidak ditemukan');
            
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@getFile: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return $this->returnPlaceholderImage($e->getMessage());
        }
    }
    
    // Helper method to search the uploads folder for potential matching files
    private function searchUploadsFolder($monitoringId)
    {
        try {
            $uploadsDir = public_path('uploads');
            if (!is_dir($uploadsDir)) {
                \Log::warning("Uploads directory doesn't exist: $uploadsDir");
                return false;
            }
            
            \Log::info("Searching uploads directory for possible matches");
            $files = scandir($uploadsDir);
            
            if (empty($files)) {
                \Log::warning("No files found in uploads directory");
                return false;
            }
            
            \Log::info("Found " . count($files) . " files in uploads directory");
            
            // Get creation date of monitoring record
            $monitoring = DB::table('monitoring')->where('id', $monitoringId)->first();
            if (!$monitoring) {
                return false;
            }
            
            $recordTimestamp = strtotime($monitoring->created_at);
            $bestMatchFile = null;
            $bestTimeDiff = PHP_INT_MAX;
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                // Check if file follows Flutter timestamp naming pattern
                if (preg_match('/^(\d+)\.(jpg|jpeg|png)$/i', $file, $matches)) {
                    $fileTimestamp = intval($matches[1] / 1000); // Convert from milliseconds to seconds
                    $timeDiff = abs($fileTimestamp - $recordTimestamp);
                    
                    \Log::info("Checking file $file: file timestamp=$fileTimestamp, record timestamp=$recordTimestamp, diff=$timeDiff");
                    
                    // If this file is closer to the record timestamp than our current best match
                    if ($timeDiff < $bestTimeDiff) {
                        $bestMatchFile = $file;
                        $bestTimeDiff = $timeDiff;
                    }
                }
            }
            
            if ($bestMatchFile && $bestTimeDiff < 86400) { // Within 24 hours
                \Log::info("Found potential match: $bestMatchFile with time difference of $bestTimeDiff seconds");
                
                // Update the database with the correct path
                DB::table('monitoring')->where('id', $monitoringId)->update([
                    'foto_url' => 'uploads/' . $bestMatchFile
                ]);
                
                return true;
            }
            
            \Log::warning("No suitable match found in uploads directory");
            return false;
            
        } catch (\Exception $e) {
            \Log::error("Error in searchUploadsFolder: " . $e->getMessage());
            return false;
        }
    }

    // Helper method to return a placeholder image
    private function returnPlaceholderImage($reason = 'Unknown error')
    {
        $placeholderPath = public_path('images/no-image.jpg');
        if (file_exists($placeholderPath)) {
            return response()->json([
                'data' => base64_encode(file_get_contents($placeholderPath)),
                'is_placeholder' => true,
                'reason' => $reason
            ]);
        }
        
        // Fallback to a tiny transparent image
        return response()->json([
            'error' => $reason,
            'data' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mO8Ww8AAj8BXkQ+xPEAAAAASUVORK5CYII=', // 1x1 transparent pixel
            'is_placeholder' => true
        ], 200);
    }

    // API Methods
    public function apiIndex()
    {
        try {
            $monitoring = Monitoring::with(['barang.lokasi', 'statusRelation'])->get();
            return response()->json([
                'status' => 'success',
                'data' => $monitoring->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nup' => $item->nup,
                        'barang' => [
                            'id' => $item->barang->id,
                            'nama_barang' => $item->barang->nama_barang,
                            'lokasi' => [
                                'id' => $item->barang->lokasi->id ?? null,
                                'nama_lokasi' => $item->barang->lokasi->nama_lokasi ?? 'N/A'
                            ]
                        ],
                        'status' => [
                            'id' => $item->statusRelation->id ?? null,
                            'nama_status' => $item->statusRelation->nama_status ?? 'N/A'
                        ],
                        'keterangan' => $item->keterangan,
                        'image_url' => $item->image ? url('storage/'.$item->image) : null,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiShow($id)
    {
        try {
            $monitoring = Monitoring::with(['barang.lokasi', 'statusRelation'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $monitoring->id,
                    'nup' => $monitoring->nup,
                    'barang' => [
                        'id' => $monitoring->barang->id,
                        'nama_barang' => $monitoring->barang->nama_barang,
                        'lokasi' => [
                            'id' => $monitoring->barang->lokasi->id ?? null,
                            'nama_lokasi' => $monitoring->barang->lokasi->nama_lokasi ?? 'N/A'
                        ]
                    ],
                    'status' => [
                        'id' => $monitoring->statusRelation->id ?? null,
                        'nama_status' => $monitoring->statusRelation->nama_status ?? 'N/A'
                    ],
                    'keterangan' => $monitoring->keterangan,
                    'image_url' => $monitoring->image ? url('storage/'.$monitoring->image) : null,
                    'created_at' => $monitoring->created_at,
                    'updated_at' => $monitoring->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiStore(Request $request)
    {
        try {
            \Log::info('API Store request received', [
                'content_type' => $request->header('Content-Type'),
                'has_file' => $request->hasFile('image') ? 'Yes' : 'No',
                'has_base64' => $request->has('image_base64') ? 'Yes' : 'No',
                'has_foto_url' => $request->has('foto_url') ? 'Yes' : 'No'
            ]);
            
            $validated = $request->validate([
                'nup' => 'required|string',
                'barang_id' => 'required|exists:barang,id',
                'status_id' => 'required|exists:status,id',
                'keterangan' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'image_base64' => 'nullable|string',
                'foto_url' => 'nullable|string'
            ]);

            // Map API fields to database fields
            $monitoringData = [
                'user_id' => User::where('NUP', $validated['nup'])->first()->id ?? null,
                'nama_barang' => $validated['barang_id'],
                'status' => $validated['status_id'],
                'keterangan' => $validated['keterangan'],
                'tanggal' => now()
            ];
            
            // Handle image upload - check for file first, then base64
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                // Store the binary image data directly in the foto field
                $monitoringData['foto'] = file_get_contents($image->getRealPath());
                \Log::info('Image processed from file upload', [
                    'size' => strlen($monitoringData['foto']),
                    'original_name' => $image->getClientOriginalName()
                ]);
            } 
            elseif ($request->has('image_base64') && !empty($request->image_base64)) {
                // Handle base64 encoded image data
                $base64Data = $request->image_base64;
                
                // Check if it includes the data:image prefix and remove it
                if (strpos($base64Data, 'data:image') === 0) {
                    $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                }
                
                // Decode the base64 data
                $imageData = base64_decode($base64Data, true);
                
                if ($imageData === false) {
                    \Log::error('Failed to decode base64 image data');
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid base64 image data'
                    ], 400);
                }
                
                $monitoringData['foto'] = $imageData;
                \Log::info('Image processed from base64 data', [
                    'size' => strlen($monitoringData['foto'])
                ]);
            }
            // Check for foto_url
            elseif ($request->has('foto_url') && !empty($request->foto_url)) {
                $fotoUrl = $request->foto_url;
                
                // Handle Flutter emulator URLs
                if (strpos($fotoUrl, '10.0.2.2:5000') !== false) {
                    $pattern = '/\/uploads\/([^\/\s]+\.(jpg|jpeg|png))/i';
                    if (preg_match($pattern, $fotoUrl, $matches)) {
                        $fotoUrl = 'uploads/' . $matches[1];
                    }
                }
                
                $monitoringData['foto_url'] = $fotoUrl;
                \Log::info('Using provided foto_url', [
                    'foto_url' => $fotoUrl
                ]);
            }

            $monitoring = Monitoring::create($monitoringData);

            return response()->json([
                'status' => 'success',
                'message' => 'Data monitoring berhasil ditambahkan',
                'data' => [
                    'id' => $monitoring->id,
                    'foto_url' => $monitoring->foto_url,
                ]
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@apiStore: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiUpdate(Request $request, $id)
    {
        try {
            $monitoring = Monitoring::findOrFail($id);

            \Log::info('API Update request received', [
                'id' => $id,
                'content_type' => $request->header('Content-Type'),
                'has_file' => $request->hasFile('image') ? 'Yes' : 'No',
                'has_base64' => $request->has('image_base64') ? 'Yes' : 'No',
                'has_foto_url' => $request->has('foto_url') ? 'Yes' : 'No'
            ]);

            $validated = $request->validate([
                'nup' => 'required|string',
                'barang_id' => 'required|exists:barang,id',
                'status_id' => 'required|exists:status,id',
                'keterangan' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'image_base64' => 'nullable|string',
                'foto_url' => 'nullable|string'
            ]);

            // Map API fields to database fields
            $monitoringData = [
                'user_id' => User::where('NUP', $validated['nup'])->first()->id ?? $monitoring->user_id,
                'nama_barang' => $validated['barang_id'],
                'status' => $validated['status_id'],
                'keterangan' => $validated['keterangan'],
                'tanggal' => now()
            ];

            // Handle image upload - check for file first, then base64
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                // Store the binary image data directly in the foto field
                $monitoringData['foto'] = file_get_contents($image->getRealPath());
                \Log::info('Update: Image processed from file upload', [
                    'size' => strlen($monitoringData['foto']),
                    'original_name' => $image->getClientOriginalName()
                ]);
                }
            elseif ($request->has('image_base64') && !empty($request->image_base64)) {
                // Handle base64 encoded image data
                $base64Data = $request->image_base64;
                
                // Check if it includes the data:image prefix and remove it
                if (strpos($base64Data, 'data:image') === 0) {
                    $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                }
                
                // Decode the base64 data
                $imageData = base64_decode($base64Data, true);
                
                if ($imageData === false) {
                    \Log::error('Update: Failed to decode base64 image data');
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid base64 image data'
                    ], 400);
                }
                
                $monitoringData['foto'] = $imageData;
                \Log::info('Update: Image processed from base64 data', [
                    'size' => strlen($monitoringData['foto'])
                ]);
            }
            // Check for foto_url
            elseif ($request->has('foto_url') && !empty($request->foto_url)) {
                $fotoUrl = $request->foto_url;
                
                // Handle Flutter emulator URLs
                if (strpos($fotoUrl, '10.0.2.2:5000') !== false) {
                    $pattern = '/\/uploads\/([^\/\s]+\.(jpg|jpeg|png))/i';
                    if (preg_match($pattern, $fotoUrl, $matches)) {
                        $fotoUrl = 'uploads/' . $matches[1];
                    }
                }
                
                $monitoringData['foto_url'] = $fotoUrl;
                \Log::info('Update: Using provided foto_url', [
                    'foto_url' => $fotoUrl
                ]);
            }

            $monitoring->update($monitoringData);

            return response()->json([
                'status' => 'success',
                'message' => 'Data monitoring berhasil diperbarui',
                'data' => [
                    'id' => $monitoring->id,
                    'foto_url' => $monitoring->foto_url,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@apiUpdate: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiDestroy($id)
    {
        try {
            $monitoring = Monitoring::findOrFail($id);
            
            // Delete image if exists
            if ($monitoring->image) {
                Storage::disk('public')->delete($monitoring->image);
            }
            
            $monitoring->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data monitoring berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Special endpoint for Flutter to upload monitoring with images
     */
    public function apiFlutterStore(Request $request)
    {
        try {
            \Log::info('Flutter API Store request received', [
                'content_type' => $request->header('Content-Type'),
                'content_length' => $request->header('Content-Length'),
                'has_file' => $request->hasFile('image') ? 'Yes' : 'No',
                'has_base64' => $request->has('image_base64') ? 'Yes' : 'No',
                'has_raw_image' => $request->has('raw_image') ? 'Yes' : 'No',
                'request_keys' => array_keys($request->all())
            ]);
            
            // Validate basic fields
            $validator = \Validator::make($request->all(), [
                'nup' => 'required|string',
                'barang_id' => 'required|exists:barang,id',
                'status_id' => 'required|exists:status,id',
                'keterangan' => 'required|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Map API fields to database fields
            $monitoringData = [
                'user_id' => User::where('NUP', $request->nup)->first()->id ?? null,
                'nama_barang' => $request->barang_id,
                'status' => $request->status_id,
                'keterangan' => $request->keterangan,
                'tanggal' => now()
            ];
            
            // Priority 1: Check for multipart file upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageData = file_get_contents($image->getRealPath());
                $monitoringData['foto'] = $imageData;
                
                \Log::info('Flutter: Image received as multipart file', [
                    'size' => strlen($imageData), 
                    'filename' => $image->getClientOriginalName(),
                    'mime' => $image->getMimeType()
                ]);
            }
            // Priority 2: Check for base64 encoded image
            elseif ($request->has('image_base64') && !empty($request->image_base64)) {
                $base64Data = $request->image_base64;
                
                // Remove data:image prefix if present
                if (strpos($base64Data, 'data:image') === 0) {
                    $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                }
                
                $imageData = base64_decode($base64Data);
                
                if ($imageData === false) {
                    \Log::error('Flutter: Invalid base64 image data');
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Format base64 tidak valid'
                    ], 400);
                }
                
                $monitoringData['foto'] = $imageData;
                \Log::info('Flutter: Image received as base64', ['size' => strlen($imageData)]);
            }
            // Priority 3: Check for raw binary image data
            elseif ($request->has('raw_image')) {
                $rawImage = $request->raw_image;
                
                if (is_string($rawImage)) {
                    $monitoringData['foto'] = $rawImage;
                    \Log::info('Flutter: Image received as raw binary', ['size' => strlen($rawImage)]);
                } else {
                    \Log::error('Flutter: Raw image is not a string');
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Format raw image tidak valid'
                    ], 400);
                }
            }
            // Priority 4: Check if image is directly in request body (some Flutter implementations)
            elseif ($request->has('image') && is_string($request->image)) {
                // This might be a base64 string or binary data
                $imageData = $request->image;
                
                // Try to detect if it's base64
                if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $imageData)) {
                    $decodedImage = base64_decode($imageData, true);
                    if ($decodedImage !== false) {
                        $imageData = $decodedImage;
                        \Log::info('Flutter: Image detected as base64 string and decoded');
                    }
                }
                
                $monitoringData['foto'] = $imageData;
                \Log::info('Flutter: Image received from direct request body', ['size' => strlen($imageData)]);
            }
            // Priority 5: Check for image token from previous upload
            elseif ($request->has('image_token')) {
                $token = $request->image_token;
                $tempPath = storage_path('app/temp/' . $token . '.jpg');
                
                if (file_exists($tempPath)) {
                    $imageData = file_get_contents($tempPath);
                    $monitoringData['foto'] = $imageData;
                    
                    // Delete the temp file after use
                    unlink($tempPath);
                    
                    \Log::info('Flutter: Image loaded from temp token', [
                        'token' => $token,
                        'size' => strlen($imageData)
                    ]);
                } else {
                    \Log::warning('Flutter: Temp image token not found', ['token' => $token]);
                }
            }
            
            $monitoring = Monitoring::create($monitoringData);
            
            // Test the stored image immediately
            $storedMonitoring = Monitoring::find($monitoring->id);
            \Log::info('Flutter: Image storage check', [
                'id' => $monitoring->id,
                'has_foto' => !empty($storedMonitoring->foto) ? 'Yes' : 'No',
                'foto_size' => !empty($storedMonitoring->foto) ? strlen($storedMonitoring->foto) : 0
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data monitoring berhasil ditambahkan',
                'data' => [
                    'id' => $monitoring->id,
                    'foto_url' => $monitoring->foto ? route('monitoring.image', $monitoring->id) : null,
                    'foto_direct_url' => $monitoring->foto ? url('/monitoring/image/'.$monitoring->id) : null,
                    'access_test' => 'Test this URL in browser to verify access',
                ]
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Flutter API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Direct binary image endpoint for Flutter
     */
    public function uploadFlutterImage(Request $request)
    {
        try {
            \Log::info('Flutter direct image upload received', [
                'content_type' => $request->header('Content-Type'),
                'content_length' => $request->header('Content-Length'),
                'request_keys' => array_keys($request->all())
            ]);
            
            // Get the raw content from the request
            $imageData = null;
            
            // Check if image is sent as a file
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageData = file_get_contents($file->getRealPath());
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                
                \Log::info('Flutter: Received image as file', [
                    'fileName' => $fileName,
                    'fileSize' => $fileSize,
                    'data_size' => strlen($imageData)
                ]);
            }
            // Check if image is sent as base64
            elseif ($request->has('image_base64')) {
                $base64 = $request->input('image_base64');
                // Remove data URI if present
                if (strpos($base64, 'data:image') === 0) {
                    $base64 = substr($base64, strpos($base64, ',') + 1);
                }
                
                $imageData = base64_decode($base64);
                \Log::info('Flutter: Received image as base64', [
                    'original_length' => strlen($base64),
                    'decoded_size' => strlen($imageData)
                ]);
            }
            // Check if image is sent in the raw request body
            elseif ($request->getContent() && strlen($request->getContent()) > 0) {
                $imageData = $request->getContent();
                \Log::info('Flutter: Received image as raw body', [
                    'data_size' => strlen($imageData)
                ]);
            }
            
            if (!$imageData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No image data found in request'
                ], 400);
            }
            
            // Generate a unique token for referencing this image
            $token = md5(uniqid() . time());
            
            // Store in temporary local storage
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            $fullPath = $tempPath . '/' . $token . '.jpg';
            file_put_contents($fullPath, $imageData);
            
            \Log::info('Flutter: Image saved to temp storage', [
                'token' => $token,
                'path' => $fullPath,
                'size' => filesize($fullPath)
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Image received and stored',
                'token' => $token,
                'size' => strlen($imageData),
                'note' => 'Use this token in your monitoring creation request'
            ]);
        } catch (\Exception $e) {
            \Log::error('Flutter image upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showDetail($id)
    {
        try {
            \Log::info("showDetail method called for monitoring ID: $id");
            $monitoring = Monitoring::with(['barang.lokasi', 'statusRelation', 'user'])->findOrFail($id);
            $rawData = DB::table('monitoring')->where('id', $id)->first();
            if ($rawData && !empty($rawData->foto_url)) {
                $monitoring->attributes['foto_url'] = str_replace('\\', '/', $rawData->foto_url); // Normalisasi path
            }
            if ($rawData && !empty($rawData->foto) && empty($monitoring->foto)) {
                $monitoring->foto = $rawData->foto;
            }
            return view('monitoring_detail', compact('monitoring'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengambil data detail');
        }
    }

    public function showLocation($locationId)
    {
        try {
            // Get the location directly from the database
            $location = DB::table('lokasi')->where('id', $locationId)->first();
            
            if (!$location) {
                return back()->with('error', 'Lokasi tidak ditemukan');
            }
            
            // Get monitoring items with their relations
            $monitoringItems = Monitoring::with(['barang.lokasi', 'statusRelation', 'user'])
                ->whereHas('barang', function($query) use ($locationId) {
                    $query->where('id_lokasi', $locationId);
                })
                ->get();
            
            // Create the location data array
            $locationData = [
                'nama_lokasi' => $location->nama_lokasi,
                'items' => $monitoringItems
            ];
            
            return view('monitoring_location', compact('locationData'));
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@showLocation: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data lokasi');
        }
    }

    // Fungsi khusus untuk memeriksa data blob dari database
    public function debugFoto($id)
    {
        try {
            // Get all data for this monitoring record
            $monitoringRecord = DB::table('monitoring')->where('id', $id)->first();
            
            if (!$monitoringRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Monitoring record tidak ditemukan',
                    'data' => null
                ]);
            }
            
            $fotoData = $monitoringRecord->foto;
            $fotoUrl = $monitoringRecord->foto_url;
            
            $info = [
                'id' => $monitoringRecord->id,
                'created_at' => $monitoringRecord->created_at,
                'updated_at' => $monitoringRecord->updated_at,
                'foto_url' => $fotoUrl,
                'foto_url_exists' => !empty($fotoUrl),
                'blob_exists' => !empty($fotoData),
                'foto_checks' => []
            ];
            
            // Check BLOB data if it exists
            if (!empty($fotoData)) {
                $info['blob_analysis'] = [
                    'type' => gettype($fotoData),
                    'size' => strlen($fotoData),
                    'is_empty' => empty($fotoData),
                    'is_null' => is_null($fotoData),
                    'first_100_bytes_hex' => bin2hex(substr($fotoData, 0, 50)),
                    'first_20_bytes_raw' => substr($fotoData, 0, 20)
                ];
                
                // Check MIME type
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($fotoData) ?: 'unknown';
                $info['blob_analysis']['mime_type'] = $mimeType;
                $info['blob_analysis']['is_image'] = str_starts_with($mimeType, 'image/');
                
                // Check image headers
                $jpgHeader = chr(0xFF) . chr(0xD8) . chr(0xFF);
                $pngHeader = chr(0x89) . 'PNG' . chr(0x0D) . chr(0x0A) . chr(0x1A) . chr(0x0A);
                $gifHeader1 = 'GIF87a';
                $gifHeader2 = 'GIF89a';
                
                $firstBytes = substr($fotoData, 0, 8);
                $info['blob_analysis']['has_jpg_header'] = strpos($firstBytes, $jpgHeader) === 0;
                $info['blob_analysis']['has_png_header'] = strpos($firstBytes, $pngHeader) === 0;
                $info['blob_analysis']['has_gif_header'] = strpos($firstBytes, $gifHeader1) === 0 || strpos($firstBytes, $gifHeader2) === 0;
            }
            
            // Check foto_url if it exists
            if (!empty($fotoUrl)) {
                $info['foto_url_analysis'] = [
                    'basename' => basename($fotoUrl),
                    'is_timestamp_format' => preg_match('/^\d+\.(jpg|jpeg|png)$/i', basename($fotoUrl)) ? 'Yes' : 'No',
                    'contains_uploads_path' => strpos($fotoUrl, 'uploads/') !== false ? 'Yes' : 'No',
                    'contains_emulator_url' => strpos($fotoUrl, '10.0.2.2') !== false ? 'Yes' : 'No'
                ];
                
                // Check various paths that might work
                $checks = [];
                
                // Check 1: Direct path as stored
                $path1 = public_path(ltrim($fotoUrl, '/'));
                $checks[] = [
                    'type' => 'Direct path as stored',
                    'path' => $path1,
                    'exists' => file_exists($path1) ? 'Yes' : 'No',
                    'readable' => is_readable($path1) ? 'Yes' : 'No',
                    'size' => file_exists($path1) ? filesize($path1) : 'N/A'
                ];
                
                // Check 2: In uploads folder with basename
                $path2 = public_path('uploads/' . basename($fotoUrl));
                $checks[] = [
                    'type' => 'In uploads folder with basename',
                    'path' => $path2,
                    'exists' => file_exists($path2) ? 'Yes' : 'No',
                    'readable' => is_readable($path2) ? 'Yes' : 'No',
                    'size' => file_exists($path2) ? filesize($path2) : 'N/A'
                ];
                
                // Check 3: If it's an emulator URL, extract the uploads path
                if (strpos($fotoUrl, '10.0.2.2') !== false) {
                    $pattern = '/\/uploads\/([^\/\s]+\.(jpg|jpeg|png))/i';
                    if (preg_match($pattern, $fotoUrl, $matches)) {
                        $path3 = public_path('uploads/' . $matches[1]);
                        $checks[] = [
                            'type' => 'Extracted from emulator URL',
                            'path' => $path3,
                            'exists' => file_exists($path3) ? 'Yes' : 'No',
                            'readable' => is_readable($path3) ? 'Yes' : 'No',
                            'size' => file_exists($path3) ? filesize($path3) : 'N/A'
                        ];
                    }
                }
                
                // List all files in uploads directory
                $uploadsDir = public_path('uploads');
                if (is_dir($uploadsDir)) {
                    $files = array_diff(scandir($uploadsDir), array('.', '..'));
                    $info['uploads_dir_files'] = array_slice($files, 0, 10); // Show first 10 files
                    $info['uploads_dir_file_count'] = count($files);
                } else {
                    $info['uploads_dir_exists'] = false;
                }
                
                $info['foto_checks'] = $checks;
            }
            
            // Solution recommendations
            $solutions = [];
            
            if (empty($fotoData) && empty($fotoUrl)) {
                $solutions[] = "Tidak ada data foto sama sekali. Upload foto baru.";
            } 
            else if (!empty($fotoUrl)) {
                // Try to find a matching file in uploads directory by timestamp
                if (preg_match('/(\d+)\.(jpg|jpeg|png)$/i', $fotoUrl, $matches)) {
                    $timestamp = $matches[1];
                    $extension = $matches[2];
                    $uploadsDir = public_path('uploads');
                    
                    if (is_dir($uploadsDir)) {
                        $files = scandir($uploadsDir);
                        $exactMatch = false;
                        
                        foreach ($files as $file) {
                            if ($file === $timestamp . '.' . $extension) {
                                $exactMatch = true;
                                $solutions[] = "File ditemukan di folder uploads dengan nama yang sama.";
                                
                                // Update the database with the correct path if needed
                                if ($fotoUrl !== 'uploads/' . $file) {
                                    DB::table('monitoring')->where('id', $id)->update([
                                        'foto_url' => 'uploads/' . $file
                                    ]);
                                    $solutions[] = "Database diupdate dengan path yang benar: 'uploads/" . $file . "'";
                                }
                                break;
                            }
                        }
                        
                        if (!$exactMatch) {
                            $solutions[] = "File dengan nama persis tidak ditemukan di folder uploads. Coba perbaiki path.";
                        }
                    }
                }
            }
            
            $info['recommended_solutions'] = $solutions;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data foto berhasil dianalisis',
                'data' => $info
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error saat debugging foto: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function testImageAccess($id)
    {
        try {
            $monitoring = Monitoring::findOrFail($id);
            
            // Check if there's a foto_url
            if (!empty($monitoring->foto_url)) {
                $fotoUrl = $monitoring->foto_url;
                $info = [
                    'id' => $monitoring->id,
                    'original_foto_url' => $monitoring->foto_url,
                    'processed_foto_url' => $monitoring->getFotoUrlAttribute(),
                    'foto_exists' => !empty($monitoring->foto) ? 'Yes' : 'No',
                    'image_details' => []
                ];
                
                // Check if it's a local path
                if (strpos($fotoUrl, 'http') !== 0) {
                    // It's a local path, check if the file exists
                    $localPath = public_path(ltrim($fotoUrl, '/'));
                    $info['local_path_full'] = $localPath;
                    $info['local_path_exists'] = file_exists($localPath) ? 'Yes' : 'No';
                    
                    if (file_exists($localPath)) {
                        $info['image_details'] = [
                            'size' => filesize($localPath),
                            'mime_type' => mime_content_type($localPath),
                            'is_readable' => is_readable($localPath) ? 'Yes' : 'No'
                        ];
                    }
                }
                
                // Check for emulator URL
                if (strpos($fotoUrl, '10.0.2.2:5000') !== false) {
                    $info['is_emulator_url'] = 'Yes';
                    // Try to extract just the path
                    $pattern = '/\/uploads\/[^\/\s]+\.(jpg|jpeg|png)/i';
                    if (preg_match($pattern, $fotoUrl, $matches)) {
                        $localPath = public_path(ltrim($matches[0], '/'));
                        $info['extracted_path'] = $matches[0];
                        $info['extracted_path_full'] = $localPath;
                        $info['extracted_path_exists'] = file_exists($localPath) ? 'Yes' : 'No';
                    }
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Image access test',
                    'info' => $info
                ]);
            } else {
                // No foto_url, check for BLOB data
                return response()->json([
                    'status' => 'success',
                    'message' => 'No foto_url, using BLOB data',
                    'info' => [
                        'id' => $monitoring->id,
                        'has_blob' => !empty($monitoring->foto) ? 'Yes' : 'No',
                        'blob_size' => !empty($monitoring->foto) ? strlen($monitoring->foto) : 0,
                        'image_url' => $monitoring->getFotoUrlAttribute()
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error testing image access: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}