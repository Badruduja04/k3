<!DOCTYPE html>
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header and Back Button -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-primary">
                    <i class="fas fa-desktop text-primary me-2"></i>
                    Detail Monitoring
                </h1>
                <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Simple Card with Clear Information -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-info-circle me-2"></i>Informasi Detail Monitoring</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-detail">
                    <tbody>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-user text-primary me-2"></i>
                                <strong>User</strong>
                            </td>
                            <td class="field-value">{{ $monitoring->user->nama ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <strong>Tanggal</strong>
                            </td>
                            <td class="field-value">{{ $monitoring->tanggal ? $monitoring->tanggal->locale('id')->translatedFormat('d F Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-box text-primary me-2"></i>
                                <strong>Nama Barang</strong>
                            </td>
                            <td class="field-value">{{ $monitoring->barang->nama_barang ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <strong>Lokasi</strong>
                            </td>
                            <td class="field-value">{{ $monitoring->barang->lokasi->nama_lokasi ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                <strong>Status</strong>
                            </td>
                            <td class="field-value">
                                <span class="status-badge 
                                    @if($monitoring->statusRelation->nama_status == 'sesuai') status-normal
                                    @elseif($monitoring->statusRelation->nama_status == 'rusak' || $monitoring->statusRelation->nama_status == 'hilang') status-error
                                    @else status-maintenance
                                    @endif">
                                    {{ $monitoring->statusRelation->nama_status ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-file-alt text-primary me-2"></i>
                                <strong>Keterangan</strong>
                            </td>
                            <td class="field-value">{{ $monitoring->keterangan ?? 'Tidak ada keterangan' }}</td>
                        </tr>
                        <tr>
                            <td class="field-name">
                                <i class="fas fa-image text-primary me-2"></i>
                                <strong>Foto</strong>
                            </td>
                            <td class="field-value">
                                @if($monitoring->foto || isset($monitoring->attributes['foto_url']))
                                <div class="image-container">
                                    <!-- Directly load image via file/base64 instead of URL -->
                                    <div class="text-center mt-2" id="imageContainer-{{ $monitoring->id }}">
                                        <div class="mb-2">
                                            <i class="fas fa-spinner fa-spin"></i> Memuat gambar...
                                        </div>
                                        <a href="#" class="foto-link" id="fotoLink-{{ $monitoring->id }}" onclick="showImageModal({{ $monitoring->id }}); return false;">
                                            <img id="fotoImg-{{ $monitoring->id }}" class="img-detail d-none" alt="Foto Monitoring">
                                        </a>
                                    </div>

                                    <!-- Debug info hidden by default -->
                                    <div class="debug-info d-none">
                                        <p>ID: {{ $monitoring->id }}</p>
                                        <p>Foto tersedia: {{ !empty($monitoring->foto) ? 'Ya' : 'Tidak' }}</p>
                                        <p>Foto URL in attribute: {{ isset($monitoring->attributes['foto_url']) ? $monitoring->attributes['foto_url'] : 'Tidak ada' }}</p>
                                        <p>Foto URL accessor: {{ $monitoring->foto_url }}</p>
                                        <p>Tipe data: {{ gettype($monitoring->foto) }}</p>
                                        <p>Ukuran data: {{ !empty($monitoring->foto) ? strlen($monitoring->foto) : 0 }} bytes</p>
                                    </div>
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
</div>

<!-- Modal untuk preview gambar -->
<div class="modal fade" id="imageModal-{{ $monitoring->id }}" tabindex="-1" aria-labelledby="imageModalLabel-{{ $monitoring->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel-{{ $monitoring->id }}">Preview Foto Monitoring</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImg-{{ $monitoring->id }}" src="#" alt="Preview Foto" style="max-width:100%; max-height:70vh; border-radius:8px; box-shadow:0 4px 16px rgba(0,0,0,0.15);">
      </div>
    </div>
  </div>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Direct load image via base64/file endpoint on page load
    loadImageDirectly({{ $monitoring->id }});
    
    // Debug mode activation
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('debug') === '1') {
        $('.debug-info').removeClass('d-none');
        console.log('Debug mode activated');
        
        // Add Debug Info button
        $('#imageContainer-{{ $monitoring->id }}').append(
            '<button class="btn btn-sm btn-secondary mt-2 ms-2" onclick="showDebugInfo({{ $monitoring->id }})">' +
            '<i class="fas fa-bug me-1"></i> Tampilkan Info Debug</button>'
        );
    }
});

function loadImageDirectly(monitoringId) {
    console.log('Loading image for monitoring ID:', monitoringId);
    
    // Get image via file endpoint (always returns base64)
    $.ajax({
        url: '/monitoring/file/' + monitoringId + '?t=' + new Date().getTime(),
        method: 'GET',
        dataType: 'json',
        cache: false,
        success: function(response) {
            console.log('File response received');
            
            if (response && response.data) {
                // Create a base64 image URL
                const imgSrc = 'data:image/jpeg;base64,' + response.data;
                
                // Update image and link
                const imgElement = $('#fotoImg-' + monitoringId);
                imgElement.attr('src', imgSrc).removeClass('d-none');
                
                // Update link but don't set href to the image
                $('#fotoLink-' + monitoringId).attr('data-img-src', imgSrc);
                
                // Remove loading indicator
                $('#imageContainer-' + monitoringId + ' .mb-2').remove();
                
                // If it's a placeholder, show message
                if (response.is_placeholder) {
                    $('#imageContainer-' + monitoringId).append(
                        '<div class="alert alert-warning mt-2">Gambar asli tidak tersedia: ' + 
                        (response.reason || 'Tidak diketahui') + '</div>' +
                        '<button onclick="tryAlternateMethod(' + monitoringId + ')" class="btn btn-sm btn-primary mt-2">' +
                        '<i class="fas fa-sync me-1"></i> Coba metode lain</button>'
                    );
                }
            } else {
                handleImageError(monitoringId, 'Tidak ada data gambar ditemukan');
            }
        },
        error: function(err) {
            console.error('Error fetching image:', err);
            handleImageError(monitoringId, 'Gagal mengambil gambar: ' + (err.statusText || 'Kesalahan server'));
        }
    });
}

function handleImageError(monitoringId, message) {
    $('#imageContainer-' + monitoringId + ' .mb-2').html(
        '<div class="alert alert-danger">' + message + '</div>'
    );
    
    // Try fallback
    $('#imageContainer-' + monitoringId).append(
        '<button onclick="tryAlternateMethod(' + monitoringId + ')" class="btn btn-sm btn-outline-primary mt-2">' +
        '<i class="fas fa-sync me-1"></i> Coba metode lain</button>' +
        '<button onclick="showDebugInfo(' + monitoringId + ')" class="btn btn-sm btn-outline-secondary mt-2 ms-2">' +
        '<i class="fas fa-bug me-1"></i> Lihat info debug</button>'
    );
}

function tryAlternateMethod(monitoringId) {
    $('#imageContainer-' + monitoringId + ' button').remove();
    $('#imageContainer-' + monitoringId + ' .alert').remove();
    $('#imageContainer-' + monitoringId + ' .mb-2').html('<i class="fas fa-spinner fa-spin"></i> Mencoba metode alternatif...');
    
    // Try direct image route
    $.ajax({
        url: '/monitoring/image/' + monitoringId + '?t=' + new Date().getTime(),
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob) {
            const url = URL.createObjectURL(blob);
            $('#fotoImg-' + monitoringId).attr('src', url).removeClass('d-none');
            $('#fotoLink-' + monitoringId).attr('href', url);
            $('#imageContainer-' + monitoringId + ' .mb-2').remove();
        },
        error: function() {
            // Final fallback - try debug endpoint to analyze the issue
            showDebugInfo(monitoringId);
        }
    });
}

function showDebugInfo(monitoringId) {
    $('#imageContainer-' + monitoringId + ' .mb-2').html('<i class="fas fa-spinner fa-spin"></i> Mengambil informasi debug...');
    
    // Use the debug endpoint
    $.ajax({
        url: '/monitoring/debug/' + monitoringId,
        method: 'GET',
        success: function(response) {
            if (response.status === 'success' && response.data) {
                const data = response.data;
                let infoHtml = '<div class="alert alert-info"><h5>Info Debug:</h5>';
                
                // Basic info
                infoHtml += '<p><strong>ID:</strong> ' + data.id + '</p>';
                infoHtml += '<p><strong>Data foto di database:</strong> ' + (data.blob_exists ? 'Ada' : 'Tidak ada') + '</p>';
                infoHtml += '<p><strong>URL foto di database:</strong> ' + (data.foto_url_exists ? data.foto_url : 'Tidak ada') + '</p>';
                
                // Foto URL checks
                if (data.foto_checks && data.foto_checks.length > 0) {
                    infoHtml += '<h6>Pengecekan Path File:</h6><ul>';
                    data.foto_checks.forEach(function(check) {
                        infoHtml += '<li>' + check.type + ': ' + 
                            (check.exists === 'Yes' ? 
                                '<span class="text-success">Ditemukan (ukuran: ' + check.size + ' bytes)</span>' : 
                                '<span class="text-danger">Tidak ditemukan</span>') + 
                            '<br><small>' + check.path + '</small></li>';
                    });
                    infoHtml += '</ul>';
                }
                
                // Files in uploads directory
                if (data.uploads_dir_files) {
                    infoHtml += '<h6>Sample file di direktori uploads (' + data.uploads_dir_file_count + ' total):</h6><ul>';
                    data.uploads_dir_files.forEach(function(file) {
                        infoHtml += '<li>' + file + '</li>';
                    });
                    infoHtml += '</ul>';
                }
                
                // Recommendations
                if (data.recommended_solutions && data.recommended_solutions.length > 0) {
                    infoHtml += '<h6 class="text-primary">Rekomendasi Solusi:</h6><ul>';
                    data.recommended_solutions.forEach(function(solution) {
                        infoHtml += '<li>' + solution + '</li>';
                    });
                    infoHtml += '</ul>';
                }
                
                infoHtml += '</div>';
                
                // Show the debug info and buttons
                $('#imageContainer-' + monitoringId + ' .mb-2').html(infoHtml);
                $('#imageContainer-' + monitoringId).append(
                    '<button onclick="tryFixPaths(' + monitoringId + ')" class="btn btn-primary mt-2">' +
                    '<i class="fas fa-wrench me-1"></i> Coba Perbaiki Otomatis</button> ' +
                    '<button onclick="loadImageDirectly(' + monitoringId + ')" class="btn btn-secondary mt-2 ms-2">' +
                    '<i class="fas fa-redo me-1"></i> Coba Lagi</button>'
                );
            } else {
                $('#imageContainer-' + monitoringId + ' .mb-2').html(
                    '<div class="alert alert-danger">Gagal mendapatkan info debug</div>'
                );
            }
        },
        error: function() {
            $('#imageContainer-' + monitoringId + ' .mb-2').html(
                '<div class="alert alert-danger">Tidak dapat mengambil info debug</div>'
            );
        }
    });
}

function tryFixPaths(monitoringId) {
    $('#imageContainer-' + monitoringId + ' .mb-2').html('<i class="fas fa-spinner fa-spin"></i> Mencoba memperbaiki path file...');
    
    $.ajax({
        url: '/monitoring/debug/' + monitoringId,
        method: 'GET',
        success: function(response) {
            if (response.status === 'success' && response.data && response.data.recommended_solutions) {
                // Try to reload image after possible fixes applied during debug
                setTimeout(function() {
                    loadImageDirectly(monitoringId);
                }, 500);
            } else {
                $('#imageContainer-' + monitoringId + ' .mb-2').html(
                    '<div class="alert alert-warning">Tidak ada solusi otomatis tersedia</div>'
                );
            }
        },
        error: function() {
            $('#imageContainer-' + monitoringId + ' .mb-2').html(
                '<div class="alert alert-danger">Gagal mencoba perbaikan otomatis</div>'
            );
        }
    });
}

function showImageModal(monitoringId) {
    // Menggunakan src dari gambar thumbnail
    var imgSrc = $('#fotoImg-' + monitoringId).attr('src');
    $('#modalImg-' + monitoringId).attr('src', imgSrc);
    var modal = new bootstrap.Modal(document.getElementById('imageModal-' + monitoringId));
    modal.show();
    return false; // Mencegah browser navigasi
}
</script>
@endpush
@endsection 