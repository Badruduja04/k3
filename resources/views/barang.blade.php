@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Data Barang</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Tambah Barang
            </button>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success fade show" role="alert">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(function() {
                    $('.alert-success').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 1000);
            </script>
            @endif

            @if(session('error'))
            <div class="alert alert-danger fade show" role="alert">
                {{ session('error') }}
            </div>
            <script>
                setTimeout(function() {
                    $('.alert-danger').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 1000);
            </script>
            @endif

            <div class="table-responsive">
                <table id="barangTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Lokasi</th>
                            <th>Kode QR</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barang as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->lokasi->nama_lokasi ?? 'N/A' }}</td>
                            <td class="text-center">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($item->qr_code ?? 'ID Barang: ' . $item->id . ' - ' . $item->nama_barang . ' | Lokasi: ' . ($item->lokasi->nama_lokasi ?? 'Tidak ada')) }}" alt="QR Code" class="img-fluid mb-2" style="max-width: 100px; background-color: white; padding: 5px;">
                                <div class="mt-2">
                                   
                                    <button type="button" class="btn btn-info btn-sm view-qr-btn" data-id="{{ $item->id }}" data-nama="{{ $item->nama_barang }}" data-lokasi="{{ $item->lokasi->nama_lokasi ?? 'Tidak ada' }}" data-qr="{{ $item->qr_code ?? '' }}">
                                        <i class="fas fa-eye"></i> Lihat
                                    </button>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm edit-btn" data-id="{{ $item->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $item->id }}">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barang.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                   
                    <div class="mb-3">
                        <label class="form-label" for="id_lokasi">Lokasi</label>
                        <select class="form-select @error('id_lokasi') is-invalid @enderror" 
                            id="id_lokasi" name="id_lokasi" required>
                            <option value="">Pilih Lokasi</option>
                            @foreach($lokasi as $lok)
                                <option value="{{ $lok->id }}" {{ old('id_lokasi') == $lok->id ? 'selected' : '' }}>
                                    {{ $lok->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="nama_barang">Nama Barang</label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" 
                            id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required>
                        @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <input type="hidden" name="qr" id="qr_code_input" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                  
                    <div class="mb-3">
                        <label class="form-label" for="edit_id_lokasi">Lokasi</label>
                        <select class="form-select @error('id_lokasi') is-invalid @enderror" 
                            id="edit_id_lokasi" name="id_lokasi" required>
                            <option value="">Pilih Lokasi</option>
                            @foreach($lokasi as $lok)
                                <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }}</option>
                            @endforeach
                        </select>
                        @error('id_lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="edit_nama_barang">Nama Barang</label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" 
                            id="edit_nama_barang" name="nama_barang" required>
                        @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <input type="hidden" name="qr" id="edit_qr_code_input" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus barang ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- QR Detail Modal -->
<div class="modal fade" id="qrDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail QR Code Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <h4 id="qr-nama-barang" class="mb-3"></h4>
                <div class="mb-3">
                    <strong>Lokasi:</strong> <span id="qr-lokasi-barang"></span>
                </div>
                <div class="qr-image-container mb-3">
                    <img id="qr-image" src="" alt="QR Code" class="img-fluid" style="max-width: 250px; background-color: white; padding: 10px;">
                </div>
                <p class="text-muted small">Pindai QR code ini untuk melihat informasi detail barang</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a id="qr-download-link" href="#" class="btn btn-success" download>
                    <i class="fas fa-download"></i> Unduh QR
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
/* DataTables Custom Styling */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.5rem 1rem;
    margin-left: 2px;
    border: 1px solid #dee2e6;
    background-color: #fff;
    color: #333;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background-color: #0061f2;
    border-color: #0061f2;
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
    color: #333 !important;
}

.dataTables_wrapper .dataTables_length select {
    padding: 0.375rem 1.75rem 0.375rem 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.dataTables_wrapper .dataTables_filter input {
    padding: 0.375rem 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    margin-left: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Setup CSRF token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    var table = $('#barangTable').DataTable({
        "pageLength": 10,
        "responsive": true,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Tidak ada data yang sesuai pencarian",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Tidak ada data yang tersedia",
            "infoFiltered": "(disaring dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        },
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [3, 4] }
        ],
        "drawCallback": function() {
            // Re-enable any disabled buttons after draw
            $('.btn').prop('disabled', false);
        }
    });

    // Reset form when modal is closed
    $('#addModal, #editModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.alert').hide();
    });

    // Add form submit handler to generate QR code before submission
    $('#addModal form').on('submit', function(e) {
        // We now use a default value of 1 for the QR code
    });

    // Edit button click handler
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var form = $('#editModal form');
        form.attr('action', '/barang/' + id);
        
        $.get('/barang/' + id + '/edit', function(response) {
            if (response.success) {
                $('#edit_nama_barang').val(response.nama_barang);
                $('#edit_id_lokasi').val(response.id_lokasi);
                $('#editModal').modal('show');
            }
        });
    });
    
    // Edit form submit handler to update QR code before submission
    $('#editModal form').on('submit', function() {
        // We now use a default value of 1 for the QR code
    });

    // Delete button click handler
    $(document).on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
            $.ajax({
                url: '/barang/' + id,
                type: 'POST',
                data: {
                    '_method': 'DELETE',
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // Disable the delete button while processing
                    $('.delete-btn').prop('disabled', true);
                },
                success: function(response) {
                    // Remove existing alerts
                    $('.alert').remove();
                    
                    // Hapus row dari DataTable dan redraw
                    table.row(row).remove().draw(false);
                    
                    // Tampilkan notifikasi sukses
                    var alertDiv = `
                        <div class="alert alert-success fade show" role="alert">
                            Data barang berhasil dihapus
                        </div>
                    `;
                    $('.card-body').prepend(alertDiv);
                    
                    // Auto hide alert after 1 second
                    setTimeout(function() {
                        $('.alert').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    // Remove existing alerts
                    $('.alert').remove();
                    
                    console.error('Error:', error);
                    var message = 'Terjadi kesalahan saat menghapus data';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    var alertDiv = `
                        <div class="alert alert-danger fade show" role="alert">
                            ${message}
                        </div>
                    `;
                    $('.card-body').prepend(alertDiv);
                    
                    // Auto hide alert after 1 second
                    setTimeout(function() {
                        $('.alert').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 1000);
                },
                complete: function() {
                    // Re-enable the delete button
                    $('.delete-btn').prop('disabled', false);
                }
            });
        }
    });
    
    // QR View button click handler
    $(document).on('click', '.view-qr-btn', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var lokasi = $(this).data('lokasi');
        var qr = $(this).data('qr');
        
        // Set data in modal
        $('#qr-nama-barang').text(nama);
        $('#qr-lokasi-barang').text(lokasi);
        
        var qrData = qr || 'ID Barang: ' + id + ' - ' + nama + ' | Lokasi: ' + lokasi;
        
        // Generate QR code URL dengan data ID barang dan lokasi
        var qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(qrData);
        $('#qr-image').attr('src', qrUrl);
        
        // Set download link with new format: nama_barang-nama_lokasi
        var sanitizedNama = nama.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        var sanitizedLokasi = lokasi.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        var filename = sanitizedNama + '-' + sanitizedLokasi + '.png';
        
        // Create a fetch request to get the QR code image
        $('#qr-download-link').off('click').on('click', function(e) {
            e.preventDefault();
            
            fetch(qrUrl)
                .then(response => response.blob())
                .then(blob => {
                    // Create a temporary link element
                    var downloadLink = document.createElement('a');
                    downloadLink.href = URL.createObjectURL(blob);
                    downloadLink.download = filename;
                    
                    // Append to the document, click, and remove
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                })
                .catch(error => {
                    console.error('Error downloading QR code:', error);
                    alert('Gagal mengunduh QR code. Silakan coba lagi.');
                });
        });
        
        // Show modal
        $('#qrDetailModal').modal('show');
    });
});
</script>
@endpush