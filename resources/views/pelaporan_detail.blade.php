@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header and Back Button -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-primary">
                    <i class="fas fa-clipboard-list text-primary me-2"></i>
                    Detail Pelaporan
                </h1>
                <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @if(isset($data))
    <!-- Simple Card with Clear Information -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-info-circle me-2"></i>Informasi Detail Pelaporan</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-detail">
                    <tbody>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-user text-primary me-2"></i>
                                <strong>Pelapor</strong>
                            </td>
                            <td class="field-value">{{ $data->user_name ?? 'Terjadi kesalahan saat mengambil data' }}</td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <strong>Waktu</strong>
                            </td>
                            <td class="field-value">{{ $data->tanggal ?? 'Terjadi kesalahan saat mengambil data' }}</td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-box text-primary me-2"></i>
                                <strong>Nama Barang</strong>
                            </td>
                            <td class="field-value">{{ $data->barang ?? 'Terjadi kesalahan saat mengambil data' }}</td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <strong>Lokasi</strong>
                            </td>
                            <td class="field-value">{{ $data->lokasi ?? 'Terjadi kesalahan saat mengambil data' }}</td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                <strong>Status</strong>
                            </td>
                            <td class="field-value">
                                <span class="status-badge 
                                    @if(strtolower($data->status ?? '') == 'sesuai') status-normal
                                    @elseif(strtolower($data->status ?? '') == 'rusak' || strtolower($data->status ?? '') == 'hilang') status-error
                                    @else status-maintenance
                                    @endif">
                                    {{ $data->status ?? 'Error' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-file-alt text-primary me-2"></i>
                                <strong>Keterangan</strong>
                            </td>
                            <td class="field-value">{{ $data->keterangan ?? 'Tidak ada keterangan' }}</td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-image text-primary me-2"></i>
                                <strong>Foto</strong>
                            </td>
                            <td class="field-value">
                                @if(isset($data->file_url) && $data->file_url)
                                <div class="image-container">
                                    <a href="{{ $data->file_url }}" target="_blank">
                                        <img src="{{ $data->file_url }}" alt="Foto" class="img-detail">
                                    </a>
                                </div>
                                @else
                                <p class="text-muted mb-0">Tidak ada foto</p>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i> Terjadi kesalahan saat memuat data. Data tidak ditemukan.
    </div>
    @endif
</div>

@push('styles')
<style>
    /* Theme Colors */
    :root {
        --primary: #1e88e5;
        --primary-dark: #1565c0;
        --primary-light: #e3f2fd;
    }
    
    /* Card styling */
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header.bg-primary {
        background-color: var(--primary) !important;
    }
    
    /* Table styling */
    .table-detail {
        font-size: 16px;
    }
    
    .table-detail td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid #e3f2fd;
    }
    
    .field-name {
        width: 30%;
        font-weight: 500;
        color: #333;
        background-color: rgba(227, 242, 253, 0.3);
    }
    
    .field-value {
        width: 70%;
        color: #333;
    }
    
    /* Make text larger and more readable */
    .table-detail, .btn, .card-header h5 {
        font-size: 1.1rem;
    }
    
    /* Status styling */
    .status-badge {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 1rem;
        font-weight: 500;
        display: inline-block;
    }
    
    .status-normal {
        background: #e8f5e9;
        color: #2e7d32;
    }
    
    .status-maintenance {
        background: #fff3e0;
        color: #ef6c00;
    }
    
    .status-error {
        background: #ffebee;
        color: #c62828;
    }
    
    /* Image styling */
    .image-container {
        text-align: center;
    }
    
    .img-detail {
        max-height: 250px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .img-detail:hover {
        transform: scale(1.02);
    }
    
    /* Button styling */
    .btn-outline-primary {
        color: var(--primary);
        border-color: var(--primary);
        font-weight: 500;
        padding: 8px 16px;
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary);
        color: white;
    }
    
    /* Increase contrast for better readability */
    strong {
        font-weight: 600;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .field-name, .field-value {
            display: block;
            width: 100%;
        }
        
        .field-name {
            padding-bottom: 0;
            border-bottom: none;
        }
    }
</style>
@endpush
@endsection 