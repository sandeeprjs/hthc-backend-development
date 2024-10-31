@php
    $user = \Illuminate\Support\Facades\Auth::user();
@endphp

<nav class="navbar side-bar">
    <div class="logo">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{URL::to('/')}}/images/logo-white.png" />
        </a>
        @if(isset($user->username))
            <div class="user-cls"> {{ $user->username .' ( '.$user->office->code.' )' }} </div>
        @endif
    </div>

    <nav class="side-bar-link w-100">
        <a class="nav-link {{ request()->url() == url('/admin/home') ? 'active' : '' }}" href="{{ url('/admin/home') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <!-- Bookings -->
        <a class="nav-link {{ request()->is('admin/bookings*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">
            <i class="fas fa-box"></i> Bookings
        </a>


        <!-- Incoming Manifests Link -->
        <a class="nav-link {{ request()->is('admin/manifests/incoming*') ? 'active' : '' }}" href="{{ route('manifests.incoming') }}">
            <i class="fas fa-arrow-down"></i> Incoming Manifests
        </a>

        <!-- Outgoing Manifests Link -->
        <a class="nav-link {{ request()->is('admin/manifests/outgoing*') ? 'active' : '' }}" href="{{ route('manifests.outgoing') }}">
            <i class="fas fa-arrow-up"></i> Outgoing Manifests
        </a>


        {{--        <!-- Deliveries -->--}}
{{--        <a class="nav-link {{ request()->is('admin/deliveries*') ? 'active' : '' }}" href="{{ route('deliveries.index') }}">--}}
{{--            <i class="fas fa-truck"></i> Deliveries--}}
{{--        </a>--}}

        <!-- Runsheets -->
        <a class="nav-link {{ request()->is('admin/runsheet*') ? 'active' : '' }}" href="{{ route('runsheet.add') }}">
            <i class="fas fa-clipboard-list"></i> Runsheets
        </a>

        <!-- Customers -->
        <a class="nav-link {{ request()->is('admin/customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
            <i class="fas fa-user"></i> Customers
        </a>

        <!-- Reports -->
        <a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}" href="{{ route('shipment.report') }}">
            <i class="fas fa-chart-line"></i> Reports
        </a>

        <!-- Settings or Configuration -->
{{--        <a class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}" href="{{ route('enabled_modules') }}">--}}
{{--            <i class="fas fa-cog"></i> Settings--}}
{{--        </a>--}}
    </nav>

    <nav class="side-bar-link logout w-100">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </nav>
</nav>

<style>
    .user-cls {
        color: white;
        text-align: center;
    }
</style>
