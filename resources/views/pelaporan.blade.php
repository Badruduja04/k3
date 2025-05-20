@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Data Pelaporan</h1>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Sortir Tanggal</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('pelaporan') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="preset_period" class="form-label">Periode</label>
                        <select id="preset_period" name="preset_period" class="form-select">
                            <option value="" {{ request('preset_period') == '' ? 'selected' : '' }}>Semua</option>
                            <option value="today" {{ request('preset_period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="yesterday" {{ request('preset_period') == 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                            <option value="week" {{ request('preset_period') == 'week' ? 'selected' : '' }}>7 Hari</option>
                            <option value="month" {{ request('preset_period') == 'month' ? 'selected' : '' }}>30 Hari</option>
                            <option value="custom" {{ request('preset_period') == 'custom' ? 'selected' : '' }}>Kustom</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 date-field" style="display: {{ request('preset_period') == 'custom' ? 'block' : 'none' }};">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-12 col-md-3 date-field" style="display: {{ request('preset_period') == 'custom' ? 'block' : 'none' }};">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('pelaporan') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="lokasipTable" class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Lokasi</th>
                            <th>Jumlah Laporan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pelaporanByLocation as $locationId => $locationData)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <span class="fw-bold">{{ $locationData['nama_lokasi'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ count($locationData['items']) }} laporan</span>
                            </td>
                            <td>
                                <a href="{{ route('pelaporan.location', $locationId) }}" class="btn btn-info btn-sm">
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
    
    /* Responsive filter adjustments */
    @media (max-width: 767.98px) {
        .date-field, #preset_period {
            margin-bottom: 1rem;
        }
        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
        .d-flex.gap-2 {
            flex-direction: column;
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#lokasipTable').DataTable({
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

    // Show or hide custom date inputs based on selection
    $('#preset_period').change(function() {
        if ($(this).val() === 'custom') {
            $('.date-field').show();
        } else {
            $('.date-field').hide();
        }
    });
});
</script>
@endpush
@endsection
