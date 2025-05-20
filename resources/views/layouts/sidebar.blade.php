<div class="sidebar">
     <div class="logo-container text-center mb-4">
        <img src="{{ asset('images/logo_tirta.png') }}" alt="Perumda Tirta Pakuan" class="logo">
    </div>

    <div class="menu-section">
        <div class="menu-header">HOME</div>
        <a href="{{ route('dashboard') }}" class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            Dashboard
        </a>
    </div>

    <div class="menu-section">
        <div class="menu-header"> DATA </div>
        <a href="{{ route('users') }}" class="menu-item {{ Request::is('users*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            Users
        </a>
        <a href="{{ route('lokasi') }}" class="menu-item {{ Request::is('lokasi') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i>
            Lokasi
        </a>
        <a href="{{ route('barang') }}" class="menu-item {{ Request::is('barang*') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            Barang
        </a>
    </div>

    <div class="menu-section">
        <div class="menu-header">MONITORING</div>
        <a href="{{ route('monitoring') }}" class="menu-item {{ Request::is('monitoring*') ? 'active' : '' }}">
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

    <div class="menu-section">
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="menu-item" style="width: 100%; background: none; border: none; text-align: left;">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </form>
    </div>
</div>

<style>
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
}

.logo-container {
    display: flex;
    align-items: center;
    padding: 1rem;
    margin-bottom: 2rem;
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

.menu-item.active {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.menu-item i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

@media (max-width: 992px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.active {
        transform: translateX(0);
    }
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
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        if (item.getAttribute('href') === currentPath) {
            item.classList.add('active');
        }
    });
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

</script>