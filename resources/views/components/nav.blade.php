@php
    $user = \Illuminate\Support\Facades\Auth::user();
@endphp

    <!-- Mobile Toggle Button (Visible on small screens) -->
<button class="sidebar-toggle d-md-none" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Navigation -->
<nav class="navbar side-bar" id="sidebar">
    <!-- Close Button (Visible only on mobile) -->
    <div class="sidebar-close d-md-none">
        <button class="btn-close-sidebar" id="closeSidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Logo and User Info -->
    <div class="logo">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ URL::to('/') }}/images/logo-white.png" alt="Logo" />
        </a>
        @if(isset($user->username))
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ $user->username }}</div>
                    <div class="user-role">{{ $user->office->code ?? 'H0001' }}</div>
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation Menu -->
    <div class="side-bar-nav w-100">
        <div class="nav-section">
            <a class="nav-link {{ request()->is('admin/home*') ? 'active' : '' }}" href="{{ url('/admin/home') }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Logistics</div>

            <div class="nav-item">
                <button class="accordion {{ request()->is('admin/bookings*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Bookings</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </button>
                <div class="submenu" style="{{ request()->is('admin/bookings*') ? 'display: block' : '' }}">
                    <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.index') ? 'active' : '' }}">
                        <i class="fas fa-circle-dot submenu-dot"></i>
                        <span>All Bookings</span>
                    </a>
                    <a href="{{ route('bookings.create') }}" class="{{ request()->routeIs('bookings.create') ? 'active' : '' }}">
                        <i class="fas fa-circle-dot submenu-dot"></i>
                        <span>Single Booking</span>
                    </a>
                    <a href="{{ route('bookings.bulk') }}" class="{{ request()->routeIs('bookings.bulk') ? 'active' : '' }}">
                        <i class="fas fa-circle-dot submenu-dot"></i>
                        <span>Bulk Booking</span>
                    </a>
                </div>
            </div>

            @if(Route::has('manifests.incoming') && Route::has('manifests.outgoing'))
                <div class="nav-item">
                    <button class="accordion {{ request()->is('admin/manifests*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        <span>Manifests</span>
                        <i class="fas fa-chevron-right submenu-icon"></i>
                    </button>
                    <div class="submenu" style="{{ request()->is('admin/manifests*') ? 'display: block' : '' }}">
                        <a href="{{ route('manifests.incoming') }}" class="{{ request()->routeIs('manifests.incoming') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Incoming Manifests</span>
                        </a>
                        <a href="{{ route('manifests.outgoing') }}" class="{{ request()->routeIs('manifests.outgoing') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Outgoing Manifests</span>
                        </a>
                    </div>
                </div>
            @endif

            @if(Route::has('dispatches.index'))
                <a href="{{ route('dispatches.index') }}" class="nav-link {{ request()->is('admin/dispatches*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>Dispatches</span>
                </a>
            @endif

            @if(Route::has('tracking.index'))
                <a href="{{ route('tracking.index') }}" class="nav-link {{ request()->is('admin/tracking*') ? 'active' : '' }}">
                    <i class="fas fa-search-location"></i>
                    <span>Tracking</span>
                </a>
            @endif

            @if(Route::has('consignments.index'))
                <a href="{{ route('consignments.index') }}" class="nav-link {{ request()->routeIs('consignments.index') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i>
                    <span>Consignments</span>
                </a>
            @endif

            @if(Route::has('runsheet.add'))
                <a href="{{ route('runsheet.add') }}" class="nav-link {{ request()->routeIs('runsheet.add') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Runsheet</span>
                </a>
            @endif
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Configuration</div>

            <div class="nav-item">
                <button class="accordion {{ request()->is('admin/master*') ? 'active' : '' }}">
                    <i class="fas fa-cogs"></i>
                    <span>Master</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </button>
                <div class="submenu" style="{{ request()->is('admin/master*') ? 'display: block' : '' }}">
                    @if(Route::has('branches.index'))
                        <a href="{{ route('branches.index') }}" class="{{ request()->routeIs('branches.index') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Branches</span>
                        </a>
                    @endif

                    @if(Route::has('franchisees.index'))
                        <a href="{{ route('franchisees.index') }}" class="{{ request()->routeIs('franchisees.index') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Franchisees</span>
                        </a>
                    @endif

                    @if(Route::has('roles.index'))
                        <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.index') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Roles</span>
                        </a>
                    @endif

                    @if(Route::has('employees.index'))
                        <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.index') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Employees</span>
                        </a>
                    @endif

                    @if(Route::has('plans.index'))
                        <a href="{{ route('plans.index') }}" class="{{ request()->routeIs('plans.index') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Plans</span>
                        </a>
                    @endif

                    @if(Route::has('pricings.index'))
                        <a href="{{ route('pricings.index') }}" class="{{ request()->routeIs('pricings.index') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Pricing</span>
                        </a>
                    @endif

                    @if(Route::has('reasons.index'))
                        <a href="{{ route('reasons.index') }}" class="{{ request()->routeIs('reasons.index') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Reasons</span>
                        </a>
                    @endif

                    @if(Route::has('customers.index'))
                        <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.index') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Customers</span>
                        </a>
                    @endif

                    @if(Route::has('pincodes.index'))
                        <a href="{{ route('pincodes.index') }}" class="{{ request()->routeIs('pincodes.index') ? 'active' : '' }}">
                            <i class="fas fa-circle-dot submenu-dot"></i>
                            <span>Pincodes</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section with Logout Option -->
    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link logout-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</nav>

<!-- Sidebar Overlay (visible only when sidebar is open on mobile) -->
<div class="sidebar-overlay d-md-none" id="sidebarOverlay"></div>

<div id="mobileNavOverlay"></div>
