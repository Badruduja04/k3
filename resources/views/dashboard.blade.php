<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/login.png') }}">
    <title>Dashboard - Tirta Pakuan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #0061f2;
            padding: 1rem;
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        
        .logo-container {
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
            flex-shrink: 0;
        }

        .logo-container img {
            width: 150px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .logo-text {
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .logo-text span {
            display: inline-block;
            background: white;
            color: #0061f2;
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            border-radius: 4px;
            margin-right: 8px;
        }

        .menu-section {
            margin-bottom: 1.5rem;
        }

        .menu-header {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
            width: calc(100% - 250px);
        }

        /* Toggle Menu Button */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1005;
            background: #0061f2;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: #0056d6;
        }

        .menu-toggle i {
            transition: transform 0.3s ease;
        }

        .menu-toggle.active i {
            transform: rotate(180deg);
        }

        /* Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .welcome-text {
            margin-bottom: 0.5rem;
        }

        .welcome-text h1 {
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
            color: #333;
        }

        .welcome-text p {
            color: #6c757d;
            margin: 0;
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 0.5rem 1rem;
            padding-left: 2.5rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: white;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        .stat-info {
            flex-grow: 1;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0061f2;
            font-size: 1.25rem;
        }

        /* Table Styles */
        .monitoring-section {
            background: linear-gradient(to bottom, #ffffff, #f8f9ff);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 6px rgba(0,97,242,0.04);
            overflow-x: auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }

        .view-all {
            color: #0061f2;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .table {
            margin-bottom: 0;
            width: 100%;
        }

        .table th {
            font-weight: 600;
            color: #0061f2;
            border-bottom: 2px solid rgba(0,97,242,0.1);
            padding: 1rem;
            white-space: nowrap;
            background: linear-gradient(to bottom, #f8f9ff, #f0f4ff);
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            color: #495057;
            border-bottom: 1px solid rgba(0,97,242,0.05);
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(to right, #f8f9ff, #f0f4ff);
        }

        .card {
            background: linear-gradient(to bottom, #ffffff, #f8f9ff);
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,97,242,0.04);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: linear-gradient(to right, #f8f9ff, #f0f4ff);
            border-bottom: 1px solid rgba(0,97,242,0.05);
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .card-header h5 {
            color: #0061f2;
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* DataTables customization */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(to bottom, #0061f2, #0056d6);
            color: white !important;
            border: none;
            border-radius: 4px;
            padding: 5px 12px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(to bottom, #f0f4ff, #e6ebff);
            color: #0061f2 !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid rgba(0,97,242,0.2);
            border-radius: 6px;
            padding: 6px 12px;
            margin-left: 8px;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: #0061f2;
            box-shadow: 0 0 0 2px rgba(0,97,242,0.1);
        }

        /* Status badges with blue theme */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-normal {
            background: linear-gradient(to right, #e8f5e9, #c8e6c9);
            color: #2e7d32;
        }

        .status-maintenance {
            background: linear-gradient(to right, #fff3e6, #ffe8cc);
            color: #ef6c00;
        }

        .status-error {
            background: linear-gradient(to right, #ffe6e6, #ffcccc);
            color: #c62828;
        }

        /* Action buttons */
        .btn-primary {
            background: linear-gradient(to bottom, #0061f2, #0056d6);
            border: none;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(to bottom, #0056d6, #004bbf);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,97,242,0.2);
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        /* User Profile */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #0061f2;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-info {
            font-size: 0.875rem;
        }

        /* Responsive breakpoints */
        @media (max-width: 1200px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .sidebar.active {
                transform: translateX(0);
                box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
            }

            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
            
            .dashboard-header {
                padding-left: 40px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .search-container {
                width: 100%;
            }
            
            .welcome-text h1 {
                font-size: 1.5rem;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .monitoring-section {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .section-title {
                font-size: 1.1rem;
            }
            
            .menu-toggle {
                top: 15px;
                left: 15px;
            }
            
            .sidebar {
                width: 50%;
                max-width: 50%;
            }
            
            .sidebar.active + .overlay {
                opacity: 1;
                visibility: visible;
            }
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) rgba(255, 255, 255, 0.1);
        }
        .menu-item-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 0.5rem;
        }
        
        .menu-item-wrapper .menu-item {
            padding-right: 30px;
        }

        .dropdown-menu-custom {
            display: none;
            background: #0061f2;
            border-radius: 0 0 8px 8px;
            padding: 0.5rem 0;
            width: 100%;
            position: static;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1) inset;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .dropdown-menu-custom .menu-item {
            padding-left: 2.5rem;
            color: white;
            text-decoration: none;
        }

        .dropdown-toggle {
            background: none;
            border: none;
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 12px;
            color: white;
            z-index: 2;
            transition: transform 0.3s;
        }
        
        .dropdown-toggle.active {
            transform: rotate(180deg);
        }

        .dropdown-menu-custom .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .detail-group {
            margin-bottom: 1.5rem;
        }

        .detail-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .detail-label i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }

        .detail-value {
            color: #666;
            margin-bottom: 0;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .modal-content {
            border-radius: 1rem;
            overflow: hidden;
        }

        .modal-header {
            padding: 1rem 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #dee2e6;
        }

        .btn-close-white {
            filter: brightness(0) invert(1);
        }

        #previewImage {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        #previewImage:hover {
            transform: scale(1.05);
        }
        
        /* Overlay for mobile sidebar */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <!-- Mobile menu toggle button -->
    <button id="menuToggle" class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar overlay for mobile -->
    <div class="overlay" id="overlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo-container text-center mb-4">
            <img src="{{ asset('images/logo_tirta.png') }}" alt="Perumda Tirta Pakuan" class="logo">
        </div>

        <div class="menu-section">
            <div class="menu-header">HOME</div>
            <a href="{{ route('dashboard') }}" class="menu-item">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-header"> DATA </div>
            <a href="{{ route('users') }}" class="menu-item">
                <i class="fas fa-users"></i>
                Users
            </a>
            <a href="{{ route('lokasi') }}" class="menu-item">
                <i class="fas fa-map-marker-alt"></i>
                Lokasi
            </a>
            <a href="{{ route('barang') }}" class="menu-item">
                <i class="fas fa-box"></i>
                Barang
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-header">MONITORING</div>
            <a href="{{ route('monitoring') }}" class="menu-item">
                <i class="fas fa-desktop"></i>
                Monitoring
            </a>
            <div class="menu-item-wrapper">
                <a href="{{ route('report.index') }}" class="menu-item {{ Request::is('report*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    Reports
                </a>
                <button type="button" class="dropdown-toggle" onclick="toggleDropdown()">
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="dropdownMenu" class="dropdown-menu-custom">
                    <a href="{{ route('pelaporan') }}" class="menu-item {{ Request::is('pelaporan*') ? 'active' : '' }}">
                        <i class="fas fa-search"></i>
                        Pelaporan
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-top: auto;">
            @csrf
            <button type="submit" class="menu-item" style="width: 100%; background: none; border: none; text-align: left;">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search...">
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="welcome-text">
            <h1>Selamat Datang, {{ $adminName }}!</h1>
            <p>Dashboard monitoring system Tirta Pakuan Bogor</p>
        </div>

        <!-- Stats -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-number">{{ $totalLokasi }}</div>
                    <div class="stat-label">TOTAL LOKASI</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-number">{{ $totalBarang }}</div>
                    <div class="stat-label">TOTAL BARANG</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-number">{{ $totalUser }}</div>
                    <div class="stat-label">TOTAL USER</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistik Status Barang</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pelaporan</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusPieChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Section -->
        <div class="row">
            <!-- Monitoring Table -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Monitoring Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="monitoringTable">
                                <thead>
                                    <tr>
                                        <th>Lokasi</th>
                                        <th>Barang</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monitoring->take(5) as $item)
                                    <tr>
                                        <td>{{ $item->barang->lokasi->nama_lokasi ?? 'N/A' }}</td>
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
                                        <td>
                                            <button onclick="showDetail({{ $item->id }})" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i>
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

            <!-- Reports Table -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pelaporan Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="reportTable">
                                <thead>
                                    <tr>
                                        <th>Lokasi</th>
                                        <th>Barang</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pelaporan->take(5) as $item)
                                    <tr>
                                        <td>{{ $item->barang->lokasi->nama_lokasi ?? 'N/A' }}</td>
                                        <td>{{ $item->barang->nama_barang ?? 'N/A' }}</td>
                                        <td>
                                            <span class="status-badge 
                                                @if(strtolower($item->status_display) == 'kehilangan') status-error
                                                @elseif(strtolower($item->status_display) == 'kerusakan') status-maintenance
                                                @elseif(strtolower($item->status_display) == 'sesuai') status-normal
                                                @else status-normal
                                                @endif">
                                                {{ $item->status_display }}
                                            </span>
                                        </td>
                                        <td>
                                            <button onclick="showPelaporanDetail({{ $item->id }})" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i>
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
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailModalLabel">Detail Monitoring</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-map-marker-alt text-primary"></i> Lokasi
                                </label>
                                <p class="detail-value" id="detailLokasi"></p>
                            </div>
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-box text-primary"></i> Nama Barang
                                </label>
                                <p class="detail-value" id="detailBarang"></p>
                            </div>
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-info-circle text-primary"></i> Status
                                </label>
                                <p class="detail-value">
                                    <span id="detailStatus" class="status-badge"></span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-calendar text-primary"></i> Tanggal
                                </label>
                                <p class="detail-value" id="detailTanggal"></p>
                            </div>
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-user text-primary"></i> User
                                </label>
                                <p class="detail-value" id="detailUser"></p>
                            </div>
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-file-alt text-primary"></i> Keterangan
                                </label>
                                <p class="detail-value" id="detailKeterangan"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-image text-primary"></i> Foto
                                </label>
                                <div class="text-center" id="detailFoto">
                                    <img src="" alt="Foto Monitoring" class="img-fluid rounded" style="max-height: 300px; display: none;" id="previewImage">
                                    <p class="text-muted" id="noImage">Tidak ada foto</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk preview gambar -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Preview Foto Monitoring</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalFullImage" src="" alt="Preview Foto" style="max-width:100%; max-height:70vh; border-radius:8px; box-shadow:0 4px 16px rgba(0,0,0,0.15);">
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal for Pelaporan Detail -->
    <div class="modal fade" id="pelaporanDetailModal" tabindex="-1" aria-labelledby="pelaporanDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pelaporanDetailModalLabel">Detail Pelaporan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-map-marker-alt text-primary"></i> Lokasi
                                </label>
                                <p class="detail-value" id="pelaporanDetailLokasi"></p>
                            </div>
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-box text-primary"></i> Nama Barang
                                </label>
                                <p class="detail-value" id="pelaporanDetailBarang"></p>
                            </div>
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-info-circle text-primary"></i> Status
                                </label>
                                <p class="detail-value">
                                    <span id="pelaporanDetailStatus" class="status-badge"></span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-calendar text-primary"></i> Waktu
                                </label>
                                <p class="detail-value" id="pelaporanDetailWaktu"></p>
                            </div>
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-user text-primary"></i> User
                                </label>
                                <p class="detail-value" id="pelaporanDetailUser"></p>
                            </div>
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-file-alt text-primary"></i> Keterangan
                                </label>
                                <p class="detail-value" id="pelaporanDetailKeterangan"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="detail-group">
                                <label class="detail-label">
                                    <i class="fas fa-image text-primary"></i> Foto
                                </label>
                                <div class="text-center" id="pelaporanDetailFoto">
                                    <img src="" alt="Foto Pelaporan" class="img-fluid rounded" style="max-height: 300px; display: none;" id="pelaporanPreviewImage">
                                    <p class="text-muted" id="pelaporanNoImage">Tidak ada foto</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.table').DataTable({
                "pageLength": 5,
                "searching": true,
                "ordering": true,
                "responsive": true,
                "dom": '<"top"f>rt<"bottom"p><"clear">',
                "language": {
                    "search": "Cari:",
                    "paginate": {
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });

            // Real-time search
            $('.search-input').on('keyup', function() {
                $('.table').DataTable().search($(this).val()).draw();
            });
            
            // Mobile menu toggle
            $('#menuToggle').on('click', function() {
                $('#sidebar').toggleClass('active');
                $('#overlay').toggleClass('active');
                $(this).toggleClass('active');
                
                if ($(this).hasClass('active')) {
                    $(this).html('<i class="fas fa-times"></i>');
                } else {
                    $(this).html('<i class="fas fa-bars"></i>');
                }
            });
            
            // Close sidebar when overlay is clicked
            $('#overlay').on('click', function() {
                $('#sidebar').removeClass('active');
                $('#menuToggle').removeClass('active').html('<i class="fas fa-bars"></i>');
                $(this).removeClass('active');
            });
            
            // Close sidebar when clicking menu item on mobile
            if (window.innerWidth < 992) {
                $('.sidebar .menu-item').on('click', function() {
                    setTimeout(function() {
                        $('#sidebar').removeClass('active');
                        $('#menuToggle').removeClass('active').html('<i class="fas fa-bars"></i>');
                        $('#overlay').removeClass('active');
                    }, 100);
                });
            }
        });
        
        function toggleDropdown() {
            var menu = document.getElementById('dropdownMenu');
            var arrow = document.querySelector('.dropdown-toggle');
            
            if ($(menu).is(':visible')) {
                $(menu).slideUp(200);
                arrow.classList.remove('active');
            } else {
                $(menu).slideDown(200);
                arrow.classList.add('active');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            var menu = document.getElementById('dropdownMenu');
            var wrapper = document.querySelector('.menu-item-wrapper');
            var arrow = document.querySelector('.dropdown-toggle');
            
            if (!wrapper.contains(event.target) && $(menu).is(':visible')) {
                $(menu).slideUp(200);
                arrow.classList.remove('active');
            }
        });

        function showDetail(id) {
            // Reset dan tampilkan loading state
            $('#detailLokasi').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#detailBarang').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#detailStatus').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#detailTanggal').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#detailUser').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#detailKeterangan').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#previewImage').hide();
            $('#noImage').hide();

            // Tampilkan modal
            $('#detailModal').modal('show');

            // Ambil data dari server
            $.ajax({
                url: '/monitoring/' + id + '/detail',
                method: 'GET',
                success: function(response) {
                    // Set nilai ke dalam modal
                    $('#detailLokasi').text(response.lokasi);
                    $('#detailBarang').text(response.nama_barang);
                    $('#detailTanggal').text(response.tanggal);
                    $('#detailUser').text(response.user);
                    $('#detailKeterangan').text(response.keterangan);
                    
                    // Set status dengan badge yang sesuai
                    const statusBadge = $('#detailStatus');
                    statusBadge.text(response.status);
                    statusBadge.removeClass().addClass('status-badge');
                    if (response.status === 'sesuai') {
                        statusBadge.addClass('status-normal');
                    } else if (response.status === 'rusak' || response.status === 'hilang') {
                        statusBadge.addClass('status-error');
                    } else {
                        statusBadge.addClass('status-maintenance');
                    }
                    
                    // Handle foto - try multiple approaches
                    loadImage(id);
                },
                error: function() {
                    // Handle error
                    $('#detailLokasi').text('Error loading data');
                    $('#detailBarang').text('Error loading data');
                    $('#detailStatus').text('Error loading data');
                    $('#detailTanggal').text('Error loading data');
                    $('#detailUser').text('Error loading data');
                    $('#detailKeterangan').text('Error loading data');
                    $('#previewImage').hide();
                    $('#noImage').text('Error loading image').show();
                }
            });
        }

        // Function to load image with multiple fallbacks
        function loadImage(id) {
            console.log('Attempting to load image for ID:', id);
            
            // Add debug info to help troubleshooting
            $('#noImage').text('Loading image...').show();
            $('#previewImage').hide();
            
            // Try the file endpoint first (returns JSON with base64) to avoid MIME issues
            $.ajax({
                url: '/monitoring/file/' + id,
                method: 'GET',
                success: function(response) {
                    console.log('File endpoint response received:', typeof response);
                    
                    if (response && response.data) {
                        const dataUrl = response.already_encoded ? 
                            'data:image/jpeg;base64,' + response.data : 
                            'data:image/jpeg;base64,' + response.data;
                        
                        console.log('Displaying image from data URL, length:', dataUrl.length);
                        
                        // Test if this data URL is valid
                        const testImg = new Image();
                        testImg.onload = function() {
                            console.log('Image loaded successfully from data URL');
                            $('#previewImage').attr('src', dataUrl).show();
                            $('#noImage').hide();
                            
                            // Add click handler for full screen
                            $('#previewImage').off('click').on('click', function() {
                                // Set the image source to the modal
                                $('#modalFullImage').attr('src', dataUrl);
                                // Show the modal
                                var imageModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
                                imageModal.show();
                                return false; // Prevent default navigation
                            });
                        };
                        
                        testImg.onerror = function() {
                            console.log('Data URL image loading failed, trying direct URL');
                            tryDirectImageUrl(id);
                        };
                        
                        testImg.src = dataUrl;
                    } else {
                        console.log('No data in response, trying direct URL');
                        tryDirectImageUrl(id);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('File endpoint error:', error, 'Status:', status);
                    tryDirectImageUrl(id);
                }
            });
        }

        // Helper function to try direct image URL
        function tryDirectImageUrl(id) {
            console.log('Trying direct image URL for ID:', id);
            const img = new Image();
            
            img.onload = function() {
                console.log('Direct image URL loaded successfully');
                $('#previewImage').attr('src', img.src).show();
                $('#noImage').hide();
                
                // Add click handler for full screen
                $('#previewImage').off('click').on('click', function() {
                    // Set the image source to the modal
                    $('#modalFullImage').attr('src', img.src);
                    // Show the modal
                    var imageModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
                    imageModal.show();
                    return false; // Prevent default navigation
                });
            };
            
            img.onerror = function() {
                console.log('All image loading attempts failed');
                $('#previewImage').hide();
                $('#noImage').text('Foto tidak tersedia atau rusak').show();
            };
            
            // Add timestamp to prevent caching
            img.src = '/monitoring/image/' + id + '?t=' + new Date().getTime();
        }

        // Tambahkan data-id ke setiap baris tabel
        $(document).ready(function() {
            $('#monitoringTable tbody tr').each(function() {
                var id = $(this).find('button').attr('onclick').match(/\d+/)[0];
                $(this).attr('data-id', id);
            });
            
            // Check for window resize to handle sidebar behavior
            $(window).resize(function() {
                if (window.innerWidth >= 992) {
                    $('#sidebar').removeClass('active');
                    $('#menuToggle').removeClass('active').html('<i class="fas fa-bars"></i>');
                    $('#overlay').removeClass('active');
                }
            });
        });

        // Initialize charts when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Line Chart
            const ctx = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [
                        {
                            label: 'Sesuai',
                            data: {!! json_encode($chartData['sesuai']) !!},
                            borderColor: '#2e7d32',
                            backgroundColor: 'rgba(46, 125, 50, 0.1)',
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#2e7d32',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointHoverBorderWidth: 3,
                            fill: true,
                            tension: 0.8
                        },
                        {
                            label: 'Kerusakan',
                            data: {!! json_encode($chartData['kerusakan']) !!},
                            borderColor: '#ef6c00',
                            backgroundColor: 'rgba(239, 108, 0, 0.1)',
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#ef6c00',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointHoverBorderWidth: 3,
                            fill: true,
                            tension: 0.8
                        },
                        {
                            label: 'Kehilangan',
                            data: {!! json_encode($chartData['kehilangan']) !!},
                            borderColor: '#c62828',
                            backgroundColor: 'rgba(198, 40, 40, 0.1)',
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#c62828',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointHoverBorderWidth: 3,
                            fill: true,
                            tension: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 13,
                                    family: "'Inter', sans-serif"
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        title: {
                            display: true,
                            text: ' Status Barang Perbulan',
                            font: {
                                size: 16,
                                family: "'Inter', sans-serif",
                                weight: '600'
                            },
                            padding: {
                                top: 10,
                                bottom: 30
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#000',
                            titleFont: {
                                size: 13,
                                family: "'Inter', sans-serif",
                                weight: '600'
                            },
                            bodyColor: '#666',
                            bodyFont: {
                                size: 12,
                                family: "'Inter', sans-serif"
                            },
                            borderColor: '#ddd',
                            borderWidth: 1,
                            padding: 12,
                            usePointStyle: true,
                            boxPadding: 6
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    family: "'Inter', sans-serif"
                                },
                                padding: 8
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [8, 4],
                                color: '#e0e0e0'
                            },
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 12,
                                    family: "'Inter', sans-serif"
                                },
                                padding: 12
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });

            // Pie Chart
            const pieCtx = document.getElementById('statusPieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Kerusakan', 'Kehilangan'],
                    datasets: [{
                        data: [
                            {{ $totalKerusakan }},
                            {{ $totalKehilangan }}
                        ],
                        backgroundColor: [
                            '#ef6c00',  // Orange untuk Kerusakan
                            '#c62828'   // Merah untuk Kehilangan
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12,
                                    family: "'Inter', sans-serif"
                                },
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Status Pelaporan Bulan Ini',
                            font: {
                                size: 16,
                                family: "'Inter', sans-serif",
                                weight: '600'
                            },
                            padding: {
                                bottom: 15
                            }
                        }
                    },
                    cutout: '65%',
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });

        function showPelaporanDetail(id) {
            // Reset dan tampilkan loading state
            $('#pelaporanDetailLokasi').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#pelaporanDetailBarang').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#pelaporanDetailStatus').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#pelaporanDetailWaktu').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#pelaporanDetailUser').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#pelaporanDetailKeterangan').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#pelaporanPreviewImage').hide();
            $('#pelaporanNoImage').hide();

            // Tampilkan modal
            $('#pelaporanDetailModal').modal('show');

            // Ambil data dari server
            $.ajax({
                url: '/pelaporan/' + id + '/detail',
                method: 'GET',
                success: function(response) {
                    // Set nilai ke dalam modal
                    $('#pelaporanDetailLokasi').text(response.lokasi);
                    $('#pelaporanDetailBarang').text(response.nama_barang);
                    $('#pelaporanDetailWaktu').text(response.waktu);
                    $('#pelaporanDetailUser').text(response.user);
                    $('#pelaporanDetailKeterangan').text(response.keterangan);
                    
                    // Set status dengan badge yang sesuai
                    const statusBadge = $('#pelaporanDetailStatus');
                    statusBadge.text(response.status);
                    statusBadge.removeClass().addClass('status-badge');
                    if (response.status === 'kehilangan') {
                        statusBadge.addClass('status-error');
                    } else if (response.status === 'kerusakan') {
                        statusBadge.addClass('status-maintenance');
                    } else {
                        statusBadge.addClass('status-normal');
                    }
                    
                    // Handle foto
                    if (response.foto_url) {
                        $('#pelaporanPreviewImage').attr('src', response.foto_url).show();
                        $('#pelaporanNoImage').hide();
                        
                        // Add click handler for full screen
                        $('#pelaporanPreviewImage').off('click').on('click', function() {
                            $('#modalFullImage').attr('src', response.foto_url);
                            var imageModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
                            imageModal.show();
                        });
                    } else {
                        $('#pelaporanPreviewImage').hide();
                        $('#pelaporanNoImage').text('Foto tidak tersedia').show();
                    }
                },
                error: function() {
                    // Handle error
                    $('#pelaporanDetailLokasi').text('Error loading data');
                    $('#pelaporanDetailBarang').text('Error loading data');
                    $('#pelaporanDetailStatus').text('Error loading data');
                    $('#pelaporanDetailWaktu').text('Error loading data');
                    $('#pelaporanDetailUser').text('Error loading data');
                    $('#pelaporanDetailKeterangan').text('Error loading data');
                    $('#pelaporanPreviewImage').hide();
                    $('#pelaporanNoImage').text('Error loading image').show();
                }
            });
        }
    </script>
</body>
</html> 