@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Data Users</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus"></i> Tambah User
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>NUP</th>
                            <th>Departemen</th>
                            <th>Sub Departemen</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->NUP }}</td>
                            <td>{{ $user->departement }}</td>
                            <td>{{ $user->sub_departement }}</td>
                            <td>{{ $user->status }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $user->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $user->id }}">
                                    <i class="fas fa-trash"></i>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NUP</label>
                        <input type="text" class="form-control" name="NUP" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Departemen</label>
                        <input type="text" class="form-control" name="departement" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sub Departemen</label>
                        <input type="text" class="form-control" name="sub_departement" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" required>
                            <option value="aktif">Aktif</option>
                            <option value="tidak">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="editEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NUP</label>
                        <input type="text" class="form-control" name="NUP" id="editNUP" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Departemen</label>
                        <input type="text" class="form-control" name="departement" id="editDepartement" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sub Departemen</label>
                        <input type="text" class="form-control" name="sub_departement" id="editSubDepartement" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" id="editStatus" required>
                            <option value="aktif">Aktif</option>
                            <option value="tidak">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // DataTable initialization - store reference for use in CRUD operations
    var userTable = $('#usersTable').DataTable({
        "pageLength": 10,
        "responsive": true,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "infoFiltered": "(disaring dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        },
        "drawCallback": function() {
            // Ensure all buttons are enabled after table redraw
            $('.btn').prop('disabled', false);
        }
    });

    // Menampilkan alert dengan waktu hilang otomatis
    function showAutoHideAlert(message, type = 'success') {
        const alertDiv = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('.container-fluid').prepend(alertDiv);
        
        // Hapus alert setelah 1 detik
        setTimeout(function() {
            $('.alert').alert('close');
        }, 1000);
    }

    // Edit button click handler
    $(document).on('click', '.edit-btn', function() {
        const userId = $(this).data('id');
        
        // Fetch user data
        $.get(`/users/${userId}`, function(user) {
            $('#editUserForm').attr('action', `/users/${userId}`);
            $('#editName').val(user.nama);
            $('#editEmail').val(user.email);
            $('#editNUP').val(user.NUP);
            $('#editDepartement').val(user.departement);
            $('#editSubDepartement').val(user.sub_departement);
            $('#editStatus').val(user.status);
            
            $('#editUserModal').modal('show');
        }).fail(function(error) {
            showAutoHideAlert('Error: Gagal mengambil data user', 'danger');
            console.error('Error:', error);
        });
    });

    // Handle add form submission
    $('#addUserModal form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#addUserModal').modal('hide');
                showAutoHideAlert('Data user berhasil ditambahkan');
                
                // Reload halaman setelah 1 detik
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(error) {
                if (error.responseJSON && error.responseJSON.errors) {
                    let errorMessage = '';
                    Object.values(error.responseJSON.errors).forEach(function(errors) {
                        errorMessage += errors.join('\n') + '\n';
                    });
                    showAutoHideAlert('Error: ' + errorMessage, 'danger');
                } else {
                    showAutoHideAlert('Error: Gagal menambahkan user', 'danger');
                }
                console.error('Error:', error);
            }
        });
    });

    // Handle edit form submission
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#editUserModal').modal('hide');
                showAutoHideAlert('Data user berhasil diupdate');
                
                // Reload halaman setelah 1 detik
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(error) {
                if (error.responseJSON && error.responseJSON.errors) {
                    let errorMessage = '';
                    Object.values(error.responseJSON.errors).forEach(function(errors) {
                        errorMessage += errors.join('\n') + '\n';
                    });
                    showAutoHideAlert('Error: ' + errorMessage, 'danger');
                } else {
                    showAutoHideAlert('Error: Gagal mengupdate user', 'danger');
                }
                console.error('Error:', error);
            }
        });
    });

    // Delete button click handler
    $(document).on('click', '.delete-btn', function() {
        const userId = $(this).data('id');
        const row = $(this).closest('tr');
        
        if (confirm('Yakin ingin menghapus user ini?')) {
            $.ajax({
                url: `/users/${userId}`,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    '_method': 'DELETE'
                },
                success: function(response) {
                    // Remove row from DataTable without page reload
                    userTable.row(row).remove().draw();
                    showAutoHideAlert('Data user berhasil dihapus');
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Gagal menghapus user';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ': ' + xhr.responseJSON.message;
                    }
                    showAutoHideAlert(errorMessage, 'danger');
                    console.error('Error:', error);
                }
            });
        }
    });
});
</script>
@endpush
@endsection 