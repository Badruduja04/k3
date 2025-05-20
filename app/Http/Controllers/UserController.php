<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'NUP' => 'required',
            'departement' => 'required',
            'sub_departement' => 'required',
            'status' => 'required|in:aktif,tidak'
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'NUP' => $request->NUP,
            'departement' => $request->departement,
            'sub_departement' => $request->sub_departement,
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'NUP' => 'required',
            'departement' => 'required',
            'sub_departement' => 'required',
            'status' => 'required|in:aktif,tidak'
        ]);

        $user = User::findOrFail($id);
        
        $userData = [
            'nama' => $request->nama,
            'email' => $request->email,
            'NUP' => $request->NUP,
            'departement' => $request->departement,
            'sub_departement' => $request->sub_departement,
            'status' => $request->status
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->back()->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
}
