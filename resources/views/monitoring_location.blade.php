<!DOCTYPE html>
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">
                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                Barang di {{ $locationData['nama_lokasi'] }}
            </h3>
            <a href="{{ route('monitoring') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="itemsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Barang</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($locationData['items']) > 0)
                            @foreach($locationData['items'] as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->barang->nama_barang ?? 'N/A' }}</td>
                                <td>
                                    <span class="status-badge 
                                        @if($item->statusRelation->nama_status == 'sesuai') status-normal
                                        @elseif($item->statusRelation->nama_status == 'rusak' || $item->statusRelation->nama_status == 'hilang') status-error
                                        @else status-maintenance
                                        @endif">
                                        {{ $item->statusRelation->nama_status ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $item->tanggal ? $item->tanggal->locale('id')->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('monitoring.detail', $item->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Belum ada data monitoring untuk lokasi ini
                                    </div>
                                </td>
                            </tr>
                        @endif
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
        $('#itemsTable').DataTable({
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