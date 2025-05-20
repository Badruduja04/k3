<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class K3ApiController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = DB::table('users')
                ->where('email', $request->email)
                ->where('password', $request->password)
                ->first();

            if ($user) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Login berhasil',
                    'data' => [
                        'id' => $user->id,
                        'nama' => $user->nama,
                        'email' => $user->email,
                        'NUP' => $user->NUP,
                        'departement' => $user->departement,
                        'sub_departement' => $user->sub_departement,
                        'status' => $user->status
                    ]
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah'
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Users API
    public function index()
    {
        $users = DB::table('users')->get();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    // Status API
    public function getStatus()
    {
        $status = Status::all();
        return response()->json([
            'status' => 'success',
            'data' => $status
        ]);
    }

    public function getStatusById($id)
    {
        $status = Status::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $status
        ]);
    }

    public function storeStatus(Request $request)
    {
        $validated = $request->validate([
            'nama_status' => 'required|string|max:255'
        ]);

        $status = Status::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Status berhasil ditambahkan',
            'data' => $status
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $status = Status::findOrFail($id);

        $validated = $request->validate([
            'nama_status' => 'required|string|max:255'
        ]);

        $status->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Status berhasil diperbarui',
            'data' => $status
        ]);
    }

    public function deleteStatus($id)
    {
        $status = Status::findOrFail($id);
        
        // Check if status is being used in monitoring
        if ($status->monitoring()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status tidak dapat dihapus karena masih digunakan dalam monitoring'
            ], 400);
        }
        
        $status->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Status berhasil dihapus'
        ]);
    }
}