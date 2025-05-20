<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - Tirta Pakuan</title>
    <link rel="icon" type="image/png" href="{{ asset('images/login.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f8f9fa;
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
            transition: transform 0.3s ease;
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
            padding: 0.8rem 1rem;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
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
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }

        .date-display {
            color: #6c757d;
        }

        /* Header Logo */
        .header-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-logo-img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0061f2;
        }

        /* User Profile */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .user-profile img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        /* Mobile Menu Toggle */
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

        /* Responsive Design */
        @media (max-width: 992px) {
            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

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
                padding-left: 40px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 50%;
                max-width: 50%;
            }
            
            .sidebar.active + .overlay {
                opacity: 1;
                visibility: visible;
            }
            
            .menu-toggle {
                top: 15px;
                left: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button id="menuToggle" class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="page-header">
            <div class="header-logo">
                <img src="{{ asset('images/login.png') }}" alt="Tirta Pakuan" class="header-logo-img">
                <span class="header-title">Tirta Pakuan</span>
            </div>
            <div>
                <div class="date-display">{{ now()->format('l, d F Y') }}</div>
            </div>
        </div>

        <!-- Content -->
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Mobile menu toggle
            $('#menuToggle').on('click', function() {
                $('.sidebar').toggleClass('active');
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
                $('.sidebar').removeClass('active');
                $('#menuToggle').removeClass('active').html('<i class="fas fa-bars"></i>');
                $(this).removeClass('active');
            });
            
            // Close sidebar when clicking menu item on mobile
            if (window.innerWidth < 992) {
                $('.sidebar .menu-item').on('click', function() {
                    setTimeout(function() {
                        $('.sidebar').removeClass('active');
                        $('#menuToggle').removeClass('active').html('<i class="fas fa-bars"></i>');
                        $('#overlay').removeClass('active');
                    }, 100);
                });
            }
            
            // Check for window resize to handle sidebar behavior
            $(window).resize(function() {
                if (window.innerWidth >= 992) {
                    $('.sidebar').removeClass('active');
                    $('#menuToggle').removeClass('active').html('<i class="fas fa-bars"></i>');
                    $('#overlay').removeClass('active');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html> 