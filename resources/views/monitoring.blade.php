<!DOCTYPE html>
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Monitoring</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="locationsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Lokasi</th>
                            <th>Jumlah Barang</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monitoringByLocation as $locationId => $locationData)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <span class="fw-bold">{{ $locationData['nama_lokasi'] }}</span>
                                    <!-- Location ID: {{ $locationId }} -->
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ count($locationData['items']) }} barang</span>
                            </td>
                            <td>
                                <a href="{{ route('monitoring.location', $locationId) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#locationsTable').DataTable({
            "pageLength": 10,
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
            }
        });
    });
</script>
@endpush 