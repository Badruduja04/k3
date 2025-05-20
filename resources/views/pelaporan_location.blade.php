@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                    Pelaporan di {{ $locationData['nama_lokasi'] }}
                </h1>
                <a href="{{ route('pelaporan') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="pelaporanItemsTable" class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Nama User</th>
                            <th>Nama Barang</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locationData['items'] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->user ? $item->user->nama : 'Terjadi kesalahan saat mengambil data' }}</td>
                            <td>{{ $item->barang ? $item->barang->nama_barang : 'Terjadi kesalahan saat mengambil data' }}</td>
                            <td>
                                @php
                                    $status_name = $item->statusrelation ? $item->statusrelation->nama_status : 'Terjadi kesalahan saat mengambil data';
                                @endphp
                                <span class="status-badge 
                                    @if($status_name == 'sesuai') status-normal
                                    @elseif($status_name == 'rusak' || $status_name == 'hilang') status-error
                                    @else status-maintenance
                                    @endif">
                                    {{ ucfirst($status_name) }}
                                </span>
                            </td>
                            <td>{{ $item->waktu ? \Carbon\Carbon::parse($item->waktu)->locale('id')->translatedFormat('d F Y H:i') : 'Terjadi kesalahan saat mengambil data' }}</td>
                            <td>
                                <a href="{{ route('pelaporan.detail_page', $item->id) }}" class="btn btn-info btn-sm">
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

@push('styles')
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
<script>
$(document).ready(function() {
    $('#pelaporanItemsTable').DataTable({
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
        }
    });
});
</script>
@endpush
@endsection 