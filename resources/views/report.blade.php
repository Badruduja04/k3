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
        <button class="btn btn-primary" onclick="generatePDF()">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportExcelModal">
            <i class="fas fa-file-excel"></i> Export Excel Data
        </button>
        <button class="btn btn-info" onclick="showMonthlyReportModal()">
            <i class="fas fa-calendar-alt"></i> Laporan Bulanan
        </button>
    </div>

    <!-- Charts Row -->
    <div class="charts-row">
        <div class="chart-container">
            <h5>Monthly Monitoring Statistics</h5>
            <canvas id="monthlyChart"></canvas>
        </div>
        <div class="chart-container">
            <h5>Location Status Overview</h5>
            <canvas id="locationChart"></canvas>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="recent-activities mt-4">
        <h5>Aktivitas Terakhir (Monitoring & Pelaporan)</h5>
        <div class="table-responsive">
            <table class="table" id="activitiesTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Lokasi</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentActivities as $index => $activity)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $activity['tanggal'] }}</td>
                        <td>{{ $activity['user'] ?? 'N/A' }}</td>
                        <td>{{ $activity['lokasi'] }}</td>
                        <td>{{ $activity['barang'] ?? 'N/A' }}</td>
                        <td>{{ $activity['jenis'] }}</td>
                        <td>
                            <span class="badge 
                                {{ $activity['status'] === 'sesuai' ? 'bg-success' : 
                                (($activity['status'] === 'kerusakan' || $activity['status'] === 'kehilangan') ? 'bg-danger' : 'bg-primary') }}">
                                {{ ucfirst($activity['status']) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
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

    .charts-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .chart-container {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .chart-container h5 {
        margin-bottom: 1rem;
        color: #333;
    }

    .export-buttons {
        display: flex;
        gap: 1rem;
    }

    .export-buttons .btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .recent-activities {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .recent-activities h5 {
        margin-bottom: 1rem;
        color: #333;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
    }

    @media (max-width: 768px) {
        .charts-row {
            grid-template-columns: 1fr;
        }

        .summary-cards {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // Monthly Statistics Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json(array_column($monthlyStats, 'month')),
            datasets: [
                {
                    label: 'Total Checks',
                    data: @json(array_column($monthlyStats, 'total_checks')),
                    borderColor: '#0061f2',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Issues Found',
                    data: @json(array_column($monthlyStats, 'issues')),
                    borderColor: '#dc3545',
                    tension: 0.4,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Location Status Chart
    const locationCtx = document.getElementById('locationChart').getContext('2d');
    new Chart(locationCtx, {
        type: 'bar',
        data: {
            labels: @json(array_column($locationStats, 'name')),
            datasets: [
                {
                    label: 'Total Items',
                    data: @json(array_column($locationStats, 'total_items')),
                    backgroundColor: '#0061f2'
                },
                {
                    label: 'Status OK',
                    data: @json(array_column($locationStats, 'status_ok')),
                    backgroundColor: '#28a745'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Initialize DataTable for activities
    $(document).ready(function() {
        $('#activitiesTable').DataTable({
            "pageLength": 5,
            "searching": true,
            "ordering": true,
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

    function generatePDF() {
        // Show loading indicator
        const loadingEl = document.createElement('div');
        loadingEl.style.position = 'fixed';
        loadingEl.style.top = '0';
        loadingEl.style.left = '0';
        loadingEl.style.width = '100%';
        loadingEl.style.height = '100%';
        loadingEl.style.backgroundColor = 'rgba(0,0,0,0.5)';
        loadingEl.style.display = 'flex';
        loadingEl.style.justifyContent = 'center';
        loadingEl.style.alignItems = 'center';
        loadingEl.style.zIndex = '9999';
        loadingEl.innerHTML = '<div style="background: white; padding: 20px; border-radius: 5px;">Generating PDF, please wait...</div>';
        document.body.appendChild(loadingEl);
        
        // Get the report container
        const element = document.querySelector('.report-container');
        
        // Temporarily hide the export buttons
        const exportButtons = document.querySelector('.export-buttons');
        const originalDisplayStyle = exportButtons.style.display;
        exportButtons.style.display = 'none';
        
        // Configure html2pdf options
        const opt = {
            margin: 10,
            filename: 'report_monitoring.pdf',
            image: { type: 'jpeg', quality: 1 },
            html2canvas: { 
                scale: 2,
                useCORS: true,
                logging: true
            },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        // Generate PDF with error handling
        html2pdf().set(opt)
            .from(element)
            .save()
            .then(() => {
                // Restore the export buttons display
                exportButtons.style.display = originalDisplayStyle;
                document.body.removeChild(loadingEl);
            })
            .catch(error => {
                console.error('PDF generation error:', error);
                // Restore the export buttons display even if there's an error
                exportButtons.style.display = originalDisplayStyle;
                document.body.removeChild(loadingEl);
                alert('Failed to generate PDF. Please try again.');
            });
    }

    function exportExcel() {
        // Redirect to the Excel export route
        window.location.href = '{{ route("report.export-excel") }}';
    }
    
    function showMonthlyReportModal() {
        // Show the monthly report modal
        $('#monthlyReportModal').modal('show');
    }
    
    function exportMonthlyReport() {
        // Submit the form to download the monthly report
        document.getElementById('monthlyReportForm').submit();
    }

    function loadMonitoringImage(id) {
        if (!id) return;
        
        // Create a new image element
        const img = new Image();
        img.className = 'img-fluid rounded';
        img.style.maxHeight = '200px';
        
        // Set up onload and onerror handlers
        img.onload = function() {
            // Image loaded successfully
            $('#activity-image-' + id).html(img);
        };
        
        img.onerror = function() {
            // Try the file endpoint as a fallback
            $.ajax({
                url: '/monitoring/file/' + id,
                method: 'GET',
                success: function(response) {
                    if (response && response.data) {
                        const dataUrl = 'data:image/jpeg;base64,' + response.data;
                        img.src = dataUrl;
                    } else {
                        $('#activity-image-' + id).html('<small class="text-muted">No image available</small>');
                    }
                },
                error: function() {
                    $('#activity-image-' + id).html('<small class="text-muted">No image available</small>');
                }
            });
        };
        
        // Start loading the image
        img.src = '/monitoring/image/' + id + '?t=' + new Date().getTime();
    }
</script>
@endpush 