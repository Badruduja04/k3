@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Data Lokasi</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLokasiModal">
                <i class="fas fa-plus"></i> Tambah Lokasi
            </button>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <script>
                    setTimeout(function() {
                        $('.alert-danger').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 1000);
                </script>
            @endif
            <div id="alert-container"></div>
            
            <div class="table-responsive">
                <table id="lokasiTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lokasi</th>
                            <th class="action-column">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lokasi as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama_lokasi }}</td>
                            <td class="action-column">
                                <div class="action-buttons">
                                    <button class="btn btn-info btn-action edit-lokasi-btn" data-id="{{ $item->id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('lokasi.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-action">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Lokasi Modal -->
<div class="modal fade" id="addLokasiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addLokasiForm" action="{{ route('lokasi.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="addFormErrors" class="alert alert-danger" style="display: none;"></div>
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
                        <label class="form-label">Nama Lokasi</label>
                        <input type="text" class="form-control" name="nama_lokasi" required>
                    </div>
                    <input type="hidden" name="latitude" value="0">
                    <input type="hidden" name="longitude" value="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lokasi Modal -->
<div class="modal fade" id="editLokasiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editLokasiForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div id="editFormErrors" class="alert alert-danger" style="display: none;"></div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lokasi</label>
                        <input type="text" class="form-control" name="nama_lokasi" id="editNamaLokasi" required>
                    </div>
                    <input type="hidden" name="latitude" id="editLatitude" value="0">
                    <input type="hidden" name="longitude" id="editLongitude" value="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
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

/* Action button styles */
.action-column {
    width: 160px;
    text-align: center;
    white-space: nowrap;
}

.btn-action {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    margin: 0 2px;
}

.btn-action i {
    margin-right: 3px;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 5px;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#lokasiTable').DataTable({
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
            { "orderable": false, "targets": 2 }
        ],
        "drawCallback": function() {
            // Make sure all buttons are enabled after redraw
            $('.btn').prop('disabled', false);
        }
    });

    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle Add Form Submit
    $('#addLokasiForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var errorDiv = $('#addFormErrors');
        errorDiv.hide();

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#addLokasiModal').modal('hide');
                showAlert('success', 'Lokasi berhasil ditambahkan');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                var errors = xhr.responseJSON;
                if (errors && errors.errors) {
                    var errorMessages = [];
                    $.each(errors.errors, function(key, value) {
                        errorMessages.push(value[0]);
                    });
                    errorDiv.html(errorMessages.join('<br>')).show();
                } else {
                    errorDiv.html('Terjadi kesalahan. Silakan coba lagi.').show();
                }
            }
        });
    });

    // Handle Edit Form Submit
    $('#editLokasiForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var errorDiv = $('#editFormErrors');
        errorDiv.hide();

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#editLokasiModal').modal('hide');
                showAlert('success', 'Lokasi berhasil diupdate');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                var errors = xhr.responseJSON;
                if (errors && errors.errors) {
                    var errorMessages = [];
                    $.each(errors.errors, function(key, value) {
                        errorMessages.push(value[0]);
                    });
                    errorDiv.html(errorMessages.join('<br>')).show();
                } else {
                    errorDiv.html('Terjadi kesalahan. Silakan coba lagi.').show();
                }
            }
        });
    });

    // Reset forms when modals are closed
    $('#addLokasiModal, #editLokasiModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.alert').hide();
    });

    // Event delegation for edit buttons
    $(document).on('click', '.edit-lokasi-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        console.log('Edit button clicked for ID:', id);
        
        if (!id) {
            console.error('No ID found on edit button');
            return;
        }
        
        editLokasi(id);
    });

    // Event delegation for delete forms
    $(document).on('submit', '.delete-form', function(event) {
        event.preventDefault();
        if (confirm('Apakah Anda yakin ingin menghapus lokasi ini?')) {
            var form = $(this);
            var url = form.attr('action');
            var row = form.closest('tr');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Use DataTable API to remove the row
                    table.row(row).remove().draw(false);
                    showAlert('success', 'Lokasi berhasil dihapus');
                },
                error: function(xhr) {
                    showAlert('error', 'Gagal menghapus lokasi');
                }
            });
        }
        return false;
    });

    // Make table variable globally accessible
    window.table = table;
});

function showAlert(type, message) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show">' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>';
    $('#alert-container').html(alertHtml);
    setTimeout(function() {
        $('#alert-container .alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 1000);
}

function editLokasi(id) {
    console.log('Fetching lokasi data for ID:', id);
    
    $.ajax({
        url: '/lokasi/' + id,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Response received:', response);
            
            $('#editLokasiForm').attr('action', '/lokasi/' + id);
            $('#editNamaLokasi').val(response.nama_lokasi);
            $('#editLatitude').val(response.latitude || 0);
            $('#editLongitude').val(response.longitude || 0);
            $('#editLokasiModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            console.error('Response:', xhr.responseText);
            showAlert('error', 'Gagal mengambil data lokasi: ' + error);
        }
    });
}
</script>
@endpush 