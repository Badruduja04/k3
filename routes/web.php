<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\K3ApiController;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\PelaporanController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\ExportController;

// Login routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User management routes
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Barang management routes
    Route::get('/barang', [BarangController::class, 'index'])->name('barang');
    Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

    // Monitoring routes
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
    Route::get('/monitoring/location/{id}', [MonitoringController::class, 'showLocation'])->name('monitoring.location');
    Route::get('/monitoring/detail/{id}', [MonitoringController::class, 'showDetail'])->name('monitoring.detail');
    Route::get('/monitoring/image/{id}', [MonitoringController::class, 'showImage'])->name('monitoring.image');
    Route::get('/monitoring/file/{id}', [MonitoringController::class, 'getFile'])->name('monitoring.file');
    Route::get('/monitoring/debug/{id}', [MonitoringController::class, 'debugFoto'])->name('monitoring.debug');
    Route::get('/monitoring/test-image/{id}', [MonitoringController::class, 'testImageAccess'])->name('monitoring.test-image');
    Route::get('/monitoring/{id}', [MonitoringController::class, 'show'])->name('monitoring.show');
    Route::get('/monitoring/{id}/detail', [MonitoringController::class, 'detail'])->name('monitoring.detail.api');

    // Lokasi management routes
    Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi');
    Route::get('/lokasi/{id}', [LokasiController::class, 'show'])->name('lokasi.show');
    Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
    Route::put('/lokasi/{id}', [LokasiController::class, 'update'])->name('lokasi.update');
    Route::delete('/lokasi/{id}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');

    // Report routes
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/pdf', [ReportController::class, 'generatePDF'])->name('report.pdf');
    Route::get('/report/excel', [ReportController::class, 'exportExcel'])->name('report.export-excel');
    Route::get('/report/monthly', [ReportController::class, 'exportMonthlyReport'])->name('report.export-monthly');
    Route::get('/report/test-data', [ReportController::class, 'testData'])->name('report.test-data');
    Route::get('/report/create-test-data', [ReportController::class, 'createTestData'])->name('report.create-test-data');

    // Routes for Pelaporan
    Route::get('/pelaporan', [PelaporanController::class, 'index'])->name('pelaporan');
    Route::get('/pelaporan/location/{id}', [PelaporanController::class, 'showLocation'])->name('pelaporan.location');
    Route::get('/pelaporan/{id}/show', [PelaporanController::class, 'show'])->name('pelaporan.show');
    Route::get('/pelaporan/{id}/detail', [PelaporanController::class, 'detail'])->name('pelaporan.detail');
    Route::get('/pelaporan/image/{id}', [PelaporanController::class, 'showImage'])->name('pelaporan.image');
    Route::get('/pelaporan/{id}/detail-page', [PelaporanController::class, 'detailPage'])->name('pelaporan.detail_page');
   
    // QR Code Routes - hanya yang dibutuhkan untuk detail modal
    Route::get('/qr/generate/{id}', [QRCodeController::class, 'generateQR'])->name('qr.generate');
    Route::get('/qr/scan/{id}', [QRCodeController::class, 'scanQR'])->name('qr.scan');

    // Export routes
    Route::get('/export/general', [ExportController::class, 'generalExport'])->name('export.general');
    
    // Serve uploaded images
    Route::get('/uploads/{filename}', function ($filename) {
        $path = public_path('uploads/' . $filename);
        if (!file_exists($path)) {
            \Log::warning("Requested image not found: $path");
            return response()->file(public_path('images/no-image.jpg'));
        }
        return response()->file($path);
    })->where('filename', '.*');
});
// API Routes
Route::prefix('api')->group(function () {
    // Auth Routes
    Route::post('/login', [ApiAuthController::class, 'login']);
    
    // Protected API Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/user', [ApiAuthController::class, 'user']);
        
        // Users API
        Route::get('/users', [K3ApiController::class, 'index']);

        // Barang Routes
        Route::get('/barang', [BarangController::class, 'apiIndex']);
        Route::get('/barang/{id}', [BarangController::class, 'apiShow']);
        Route::post('/barang', [BarangController::class, 'apiStore']);
        Route::put('/barang/{id}', [BarangController::class, 'apiUpdate']);
        Route::delete('/barang/{id}', [BarangController::class, 'apiDestroy']);

        // Lokasi Routes
        Route::get('/lokasi', [LokasiController::class, 'apiIndex']);
        Route::get('/lokasi/{id}', [LokasiController::class, 'apiShow']);
        Route::post('/lokasi', [LokasiController::class, 'apiStore']);
        Route::put('/lokasi/{id}', [LokasiController::class, 'apiUpdate']);
        Route::delete('/lokasi/{id}', [LokasiController::class, 'apiDestroy']);

        // Monitoring Routes
        Route::get('/monitoring', [MonitoringController::class, 'apiIndex']);
        Route::get('/monitoring/{id}', [MonitoringController::class, 'apiShow']);
        Route::post('/monitoring', [MonitoringController::class, 'apiStore']);
        Route::put('/monitoring/{id}', [MonitoringController::class, 'apiUpdate']);
        Route::delete('/monitoring/{id}', [MonitoringController::class, 'apiDestroy']);

        // Status Routes
        Route::get('/status', [K3ApiController::class, 'getStatus']);
        Route::get('/status/{id}', [K3ApiController::class, 'getStatusById']);
        Route::post('/status', [K3ApiController::class, 'storeStatus']);
        Route::put('/status/{id}', [K3ApiController::class, 'updateStatus']);
        Route::delete('/status/{id}', [K3ApiController::class, 'deleteStatus']);

        // QR Code Routes
        Route::get('/qr/scan/{id}', [QRCodeController::class, 'apiScanQR']);
    });
});

