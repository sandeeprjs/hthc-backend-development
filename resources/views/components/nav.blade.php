@php
    $user = \Illuminate\Support\Facades\Auth::user();
@endphp

<nav class="navbar side-bar">
    <!-- Logo and User Info -->
    <div class="logo">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ URL::to('/') }}/images/logo-white.png" alt="Logo" />
        </a>
        @if(isset($user->username))
            <div class="user-cls">
                {{ $user->username .' ( '.$user->office->code.' )' }}
            </div>
        @endif
    </div>

    <!-- Sidebar Links -->
    <nav class="side-bar-link w-100">
        <!-- Dashboard -->
        <a class="nav-link {{ request()->url() == url('/admin/home') ? 'active' : '' }}" href="{{ url('/admin/home') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <!-- Bookings -->
        <div class="nav-item">
            <button class="accordion {{ request()->is('admin/bookings*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Bookings
                <i class="fas fa-chevron-down submenu-icon"></i>
            </button>
            <div class="submenu">
                <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.index') ? 'active' : '' }}">All Bookings</a>
                <a href="{{ route('bookings.create') }}" class="{{ request()->routeIs('bookings.create') ? 'active' : '' }}">Single Booking</a>
                <a href="{{ route('bookings.bulk') }}" class="{{ request()->routeIs('bookings.bulk') ? 'active' : '' }}">Bulk Booking</a>
            </div>
        </div>

        <!-- Manifests -->
        <div class="nav-item">
            <button class="accordion {{ request()->is('admin/manifests*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Manifests
                <i class="fas fa-chevron-down submenu-icon"></i>
            </button>
            <div class="submenu">
                <a href="{{ route('manifests.incoming') }}" class="{{ request()->routeIs('manifests.incoming') ? 'active' : '' }}">Incoming Manifests</a>
                <a href="{{ route('manifests.outgoing') }}" class="{{ request()->routeIs('manifests.outgoing') ? 'active' : '' }}">Outgoing Manifests</a>
            </div>
        </div>

        <!-- Dispatches -->
        <a class="nav-link {{ request()->is('admin/dispatches*') ? 'active' : '' }}" href="{{ route('dispatches.index') }}">
            <i class="fas fa-shipping-fast"></i> Dispatches
        </a>

        <!-- Tracking -->
        <a class="nav-link {{ request()->is('admin/tracking*') ? 'active' : '' }}" href="{{ route('tracking.index') }}">
            <i class="fas fa-map-marker-alt"></i> Tracking
        </a>

        <!-- Master -->
        <div class="nav-item">
            <button class="accordion {{ request()->is('admin/master*') ? 'active' : '' }}">
                <i class="fas fa-cogs"></i> Master
                <i class="fas fa-chevron-down submenu-icon"></i>
            </button>
            <div class="submenu">
                <a href="{{ route('branches.index') }}" class="{{ request()->routeIs('branches.index') ? 'active' : '' }}">Branches</a>
                <a href="{{ route('subscriptions.index') }}" class="{{ request()->routeIs('subscriptions.index') ? 'active' : '' }}">Subscriptions</a>
                <a href="{{ route('pricings.index') }}" class="{{ request()->routeIs('pricings.index') ? 'active' : '' }}">Pricings</a>
                <a href="{{ route('franchisees.index') }}" class="{{ request()->routeIs('franchisees.index') ? 'active' : '' }}">Franchisees</a>
                <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.index') ? 'active' : '' }}">Customers</a>
                <a href="{{ route('pincodes.index') }}" class="{{ request()->routeIs('pincodes.index') ? 'active' : '' }}">Pincodes</a>
                <a href="{{ route('plans.index') }}" class="{{ request()->routeIs('plans.index') ? 'active' : '' }}">Plans</a> <!-- New "Plans" menu -->
            </div>
        </div>

        <!-- Consignments -->
        <a class="nav-link {{ request()->is('admin/consignments*') ? 'active' : '' }}" href="{{ route('consignments.index') }}">
            <i class="fas fa-truck-loading"></i> Consignments
        </a>

        <!-- Runsheet -->
        <a class="nav-link {{ request()->is('admin/runsheet*') ? 'active' : '' }}" href="{{ route('runsheet.add') }}">
            <i class="fas fa-clipboard-list"></i> Runsheet
        </a>

        <!-- RTO -->
        <div class="nav-item">
            <button class="accordion {{ request()->is('admin/returns*') ? 'active' : '' }}">
                <i class="fas fa-undo-alt"></i> RTO
                <i class="fas fa-chevron-down submenu-icon"></i>
            </button>
            <div class="submenu">
                <a href="{{ route('returns.incoming.create') }}" class="{{ request()->routeIs('returns.incoming.create') ? 'active' : '' }}">Incoming Returns</a>
                <a href="{{ route('returns.outgoing.create') }}" class="{{ request()->routeIs('returns.outgoing.create') ? 'active' : '' }}">Outgoing Returns</a>
            </div>
        </div>

        <!-- Reports -->
        <div class="nav-item">
            <button class="accordion {{ request()->is('admin/reports*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Reports
                <i class="fas fa-chevron-down submenu-icon"></i>
            </button>
            <div class="submenu">
                <a href="{{ route('shipment.report') }}" class="{{ request()->routeIs('shipment.report') ? 'active' : '' }}">Shipment Report</a>
                <a href="{{ route('salesByPartner.report') }}" class="{{ request()->routeIs('salesByPartner.report') ? 'active' : '' }}">Sales Report</a>
            </div>
        </div>

        <!-- Logout -->
        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </nav>
</nav>

<style>
    /* Sidebar General Styling */
    .navbar.side-bar {
        background-color: #1c2536;
        color: #ffffff;
        width: 250px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        padding: 20px 0;
        overflow-y: auto;
    }

    .logo {
        text-align: center;
        margin-bottom: 20px;
    }

    .user-cls {
        color: #bdc3c7;
        font-size: 0.9em;
        margin-top: 5px;
    }

    .nav-link,
    .accordion {
        display: flex;
        align-items: center;
        color: #bdc3c7;
        padding: 10px 15px;
        text-decoration: none;
        border: none;
        background: none;
        font-size: 1em;
        cursor: pointer;
        width: 100%;
        text-align: left;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .nav-link:hover,
    .accordion:hover,
    .nav-link.active,
    .accordion.active {
        background-color: #34495e;
        color: #ffffff;
    }

    .submenu-icon {
        margin-left: auto;
        transition: transform 0.3s ease;
    }

    .accordion.active .submenu-icon {
        transform: rotate(180deg);
    }

    .submenu {
        display: none;
        overflow: hidden;
        padding-left: 15px;
    }

    .submenu a {
        display: block;
        padding: 8px 15px;
        color: #bdc3c7;
        text-decoration: none;
    }

    .submenu a:hover {
        color: #ffffff;
        background-color: #2c3e50;
    }

    .nav-link i {
        margin-right: 10px; /* Add spacing between the icon and text */
    }

    .accordion i.fas {
        margin-right: 10px;
    }

    .submenu-icon {
        margin-left: 5px; /* Add spacing before the submenu icon */
    }

    .nav-link,
    .accordion {
        color: #eff3f5 !important;
    }
</style>

<script>
    document.querySelectorAll('.accordion').forEach(button => {
        button.addEventListener('click', () => {
            const panel = button.nextElementSibling;

            // Toggle submenu visibility
            const isVisible = panel.style.display === 'block';
            document.querySelectorAll('.submenu').forEach(p => (p.style.display = 'none'));
            panel.style.display = isVisible ? 'none' : 'block';

            // Toggle submenu icon
            button.classList.toggle('active', !isVisible);
        });
    });
</script>
