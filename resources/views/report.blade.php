@extends('layouts.app')

@section('content')
<div class="report-container">
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="card-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-content">
                <h3>{{ $summaryData['total_users'] }}</h3>
                <p>Total Users</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon bg-success">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="card-content">
                <h3>{{ $summaryData['total_locations'] }}</h3>
                <p>Total Lokasi</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="card-icon bg-info">
                <i class="fas fa-box"></i>
            </div>
            <div class="card-content">
                <h3>{{ $summaryData['total_items'] }}</h3>
                <p>Total Barang</p>
            </div>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="export-buttons mb-4">
        <!-- Hapus tombol Export PDF -->
        <!--<button class="btn btn-primary" onclick="generatePDF()">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>-->
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportExcelModal">
            <i class="fas fa-file-excel"></i> Export Excel Data
        </button>
        <button class="btn btn-info" onclick="showMonthlyReportModal()">
            <i class="fas fa-calendar-alt"></i> Laporan Bulanan
        </button>
    </div>

    <!-- Monthly Report Modal -->
    <div class="modal fade" id="monthlyReportModal" tabindex="-1" aria-labelledby="monthlyReportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="monthlyReportModalLabel">Pilih Periode Laporan Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="monthlyReportForm" action="{{ route('report.export-monthly') }}" method="GET">
                        <div class="mb-3">
                            <label for="month" class="form-label">Bulan</label>
                            <select class="form-select" id="month" name="month">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $i, 1)->locale('id')->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="year" class="form-label">Tahun</label>
                            <select class="form-select" id="year" name="year">
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="exportMonthlyReport()">
                        <i class="fas fa-download"></i> Export Laporan Bulanan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export Excel Dinamis -->
    <div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exportExcelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportExcelModalLabel">Pilih Data Export Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="exportExcelForm" method="GET" action="{{ route('export.general') }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="fw-bold">Pilih Data yang Ingin Diexport:</label><br>
                            <label><input type="checkbox" name="data[]" value="users"> Users</label><br>
                            <label><input type="checkbox" name="data[]" value="barang"> Barang</label><br>
                            <label><input type="checkbox" name="data[]" value="lokasi"> Lokasi</label><br>
                            <label><input type="checkbox" name="data[]" value="monitoring"> Monitoring</label><br>
                            <label><input type="checkbox" name="data[]" value="pelaporan_kerusakan"> Pelaporan Kerusakan</label><br>
                            <label><input type="checkbox" name="data[]" value="pelaporan_kehilangan"> Pelaporan Kehilangan</label><br>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-download"></i> Export Excel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .report-container {
        padding: 1rem;
    }

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }

    .summary-card:hover {
        transform: translateY(-2px);
    }

    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .card-content h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .card-content p {
        margin: 0;
        color: #6c757d;
    }

    .export-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .export-buttons .btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
    }

    .status-badge {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-block;
        text-transform: capitalize;
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

    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.875rem;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
    }

    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
    }

    .card-header {
        border-bottom: 1px solid #e3e6f0;
    }

    @media (max-width: 768px) {
        .summary-cards {
            grid-template-columns: 1fr;
        }

        .export-buttons {
            flex-direction: column;
        }

        .export-buttons .btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#monitoringTable, #pelaporanTable').DataTable({
            "pageLength": 10,
            "responsive": true,
            "order": [[0, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": 5 },
                { "width": "5%", "targets": 0 },
                { "width": "20%", "targets": [1, 2] },
                { "width": "15%", "targets": [3, 4] },
                { "width": "10%", "targets": 5 }
            ],
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

    function exportExcel() {
        window.location.href = '{{ route("report.export-excel") }}';
    }
    
    function showMonthlyReportModal() {
        $('#monthlyReportModal').modal('show');
    }
    
    function exportMonthlyReport() {
        // Show loading state
        const exportBtn = document.querySelector('#monthlyReportModal .btn-primary');
        const originalText = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengexport...';
        exportBtn.disabled = true;

        // Get form data
        const form = document.getElementById('monthlyReportForm');
        const formData = new FormData(form);

        // Make AJAX request
        fetch(form.action + '?' + new URLSearchParams(formData).toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.blob();
        })
        .then(blob => {
            // Create download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            const monthName = document.getElementById('month').options[document.getElementById('month').selectedIndex].text;
            const year = document.getElementById('year').value;
            a.href = url;
            a.download = `laporan-bulanan-${monthName}-${year}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            // Close modal and reset button
            $('#monthlyReportModal').modal('hide');
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengexport laporan bulanan. Silakan coba lagi.');
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        });
    }
</script>
@endpush