<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Lokasi;
use App\Models\Barang;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BarangController extends Controller
{
    // Web Methods
    public function index()
    {
        $barang = Barang::with('lokasi')->get();
        $lokasi = Lokasi::all();
        return view('barang', compact('barang', 'lokasi'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_barang' => 'required|string|max:255',
                'id_lokasi' => 'required|exists:lokasi,id'
            ], [
                'nama_barang.required' => 'Nama barang harus diisi',
                'nama_barang.max' => 'Nama barang maksimal 255 karakter',
                'id_lokasi.required' => 'Lokasi harus dipilih',
                'id_lokasi.exists' => 'Lokasi yang dipilih tidak valid'
            ]);

            DB::beginTransaction();
            try {
                // Create barang without qr field first
                $barang = Barang::create([
                    'nama_barang' => $validated['nama_barang'],
                    'id_lokasi' => $validated['id_lokasi']
                ]);
                
                // Update with qr field set to the same as ID
                $barang->qr = $barang->id;
                $barang->save();
                
                DB::commit();
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Barang berhasil ditambahkan',
                        'data' => $barang
                    ]);
                }
                
                return redirect()->back()->with('success', 'Barang berhasil ditambahkan');
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error saving barang: ' . $e->getMessage());
                
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
            Log::error('Error validating barang: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'errors' => $e instanceof ValidationException ? $e->errors() : null
                ], 422);
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $barang = Barang::findOrFail($id);
            return response()->json([
                'success' => true,
                'nama_barang' => $barang->nama_barang,
                'id_lokasi' => $barang->id_lokasi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required',
            'id_lokasi' => 'required|exists:lokasi,id'
        ], [
            'nama_barang.required' => 'Nama barang harus diisi',
            'id_lokasi.required' => 'Lokasi harus dipilih',
            'id_lokasi.exists' => 'Lokasi yang dipilih tidak valid'
        ]);

        try {
            $barang = Barang::findOrFail($id);
            $barang->update([
                'nama_barang' => $request->nama_barang,
                'id_lokasi' => $request->id_lokasi,
                'qr' => $id // Set qr value to the barang ID
            ]);

            return redirect()->route('barang')->with('success', 'Barang berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->route('barang')->with('error', 'Gagal mengupdate barang: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $barang = Barang::findOrFail($id);
            $barang->delete();

            return redirect()->route('barang')->with('success', 'Barang berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('barang')->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $barang = Barang::with('lokasi')->findOrFail($id);
        return response()->json($barang);
    }

    // API Methods
    public function apiIndex()
    {
        $barang = Barang::with('lokasi')->get();
        return response()->json([
            'status' => 'success',
            'data' => $barang->map(function($item) {
                return [
                    'id' => $item->id,
                    'nama_barang' => $item->nama_barang,
                    'id_lokasi' => $item->id_lokasi,
                    'nama_lokasi' => $item->lokasi->nama_lokasi ?? 'N/A',
                ];
            })
        ]);
    }

    public function apiShow($id)
    {
        $barang = Barang::with('lokasi')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'id_lokasi' => $barang->id_lokasi,
                'nama_lokasi' => $barang->lokasi->nama_lokasi ?? 'N/A',
            ]
        ]);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'id_lokasi' => 'required|exists:lokasi,id'
        ]);

        // Create the barang without qr field first
        $barang = Barang::create($validated);
        
        // Update with qr field set to the same as ID
        $barang->qr = $barang->id;
        $barang->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data barang berhasil ditambahkan',
            'data' => [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'id_lokasi' => $barang->id_lokasi,
                'nama_lokasi' => $barang->lokasi->nama_lokasi ?? 'N/A',
            ]
        ], 201);
    }

    public function apiUpdate(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'id_lokasi' => 'required|exists:lokasi,id'
        ]);

        // Add qr field to match the barang ID
        $validated['qr'] = $id;
        $barang->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data barang berhasil diperbarui',
            'data' => [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'id_lokasi' => $barang->id_lokasi,
                'nama_lokasi' => $barang->lokasi->nama_lokasi ?? 'N/A',
            ]
        ]);
    }

    public function apiDestroy($id)
    {
        $barang = Barang::findOrFail($id);
        
        // Check if barang is being used in monitoring
        if ($barang->monitoring()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Barang tidak dapat dihapus karena masih digunakan dalam monitoring'
            ], 400);
        }
        
        $barang->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data barang berhasil dihapus'
        ]);
    }
} 
