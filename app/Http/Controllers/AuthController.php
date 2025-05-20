<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'nup' => 'required',
            'password' => 'required',
        ]);

        // Find admin by nama and password directly from database
        $admin = DB::table('admin')
            ->where('nup', $request->nup)
            ->where('password', $request->password)
            ->first();

        if ($admin) {
            // Create admin model instance for Auth
            $adminModel = new Admin();
            $adminModel->id = $admin->id;
            $adminModel->nup = $admin->nup;
            $adminModel->nama = $admin->nama;
            $adminModel->password = $admin->password;
            $adminModel->email = $admin->email;
            
            Auth::login($adminModel);
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('status', 'Login berhasil!');
        }

        // If authentication fails
        return back()->withErrors([
            'nup' => 'NUP atau password salah.',
        ])->withInput($request->only('nup'));
    }

    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}