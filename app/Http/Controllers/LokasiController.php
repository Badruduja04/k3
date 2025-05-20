<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::all();
        return view('lokasi', compact('lokasi'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_lokasi' => 'required|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric'
            ], [
                'nama_lokasi.required' => 'Nama lokasi harus diisi',
                'nama_lokasi.max' => 'Nama lokasi maksimal 255 karakter'
            ]);

            DB::beginTransaction();
            try {
                $lokasi = new Lokasi();
                $lokasi->nama_lokasi = $validated['nama_lokasi'];
                $lokasi->latitude = $request->input('latitude', 0);
                $lokasi->longitude = $request->input('longitude', 0);
                $lokasi->save();
                
                DB::commit();

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Lokasi berhasil ditambahkan',
                        'data' => $lokasi
                    ]);
                }

                return redirect()->back()->with('success', 'Lokasi berhasil ditambahkan');
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error saving lokasi: ' . $e->getMessage());
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menyimpan data: ' . $e->getMessage()
                    ], 500);
                }

                return redirect()->back()
                    ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
                    ->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Error validating lokasi: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        return response()->json($lokasi);
    }

    public function update(Request $request, $id)
    {
        try {
            $lokasi = Lokasi::findOrFail($id);

            $validated = $request->validate([
                'nama_lokasi' => 'required|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric'
            ]);

            $lokasi->nama_lokasi = $validated['nama_lokasi'];
            $lokasi->latitude = $request->input('latitude', $lokasi->latitude);
            $lokasi->longitude = $request->input('longitude', $lokasi->longitude);
            $lokasi->save();

            return redirect()->back()->with('success', 'Lokasi berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating lokasi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengupdate data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $lokasi = Lokasi::findOrFail($id);
            
            // Check if any barang uses this location
            $barangCount = Barang::where('id_lokasi', $id)->count();
            
            if ($barangCount > 0) {
                return redirect()->back()->with('error', 'Lokasi tidak dapat dihapus karena masih digunakan oleh barang');
            }
            
            $lokasi->delete();
            return redirect()->back()->with('success', 'Lokasi berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting lokasi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function apiIndex()
    {
        $lokasi = Lokasi::all();
        return response()->json([
            'status' => 'success',
            'data' => $lokasi
        ]);
    }

    public function apiShow($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $lokasi
        ]);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        $validated['latitude'] = $validated['latitude'] ?? 0;
        $validated['longitude'] = $validated['longitude'] ?? 0;

        $lokasi = Lokasi::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Lokasi berhasil ditambahkan',
            'data' => $lokasi
        ], 201);
    }

    public function apiUpdate(Request $request, $id)
    {
        $lokasi = Lokasi::findOrFail($id);

        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        $lokasi->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Lokasi berhasil diperbarui',
            'data' => $lokasi
        ]);
    }

    public function apiDestroy($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        
        // Check if any barang uses this location
        $barangCount = Barang::where('id_lokasi', $id)->count();
        
        if ($barangCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi tidak dapat dihapus karena masih digunakan oleh barang'
            ], 400);
        }
        
        $lokasi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Lokasi berhasil dihapus'
        ]);
    }
} 