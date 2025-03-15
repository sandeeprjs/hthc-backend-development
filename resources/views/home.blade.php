@extends('layouts.app')

@section('content')
    <div class="container-fluid dashboard-container">
        <div class="row pt-3">
            <div class="col-lg-12">
                <main>
                    @php
                        $user = \Illuminate\Support\Facades\Auth::user();
                    @endphp

                        <!-- Dashboard Header with Welcome Message -->
                    <div class="dashboard-header">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-6">
                                <h4 class="mb-0">Today's Summary</h4>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <h5 class="welcome-message">
                                    @foreach($user->roles as $role)
                                        @if($role->id == 1)
                                            Welcome Administrator
                                        @else
                                            Welcome {{$user->first_name}} {{$user->last_name}}
                                        @endif
                                    @endforeach
                                </h5>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row stats-row">
                            <div class="col-6 col-md-3 mb-3">
                                <div class="stats-card bookings-card">
                                    <div class="card-icon">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="card-data">
                                        <h2>{{ $bookingsToday }}</h2>
                                        <p>Bookings</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="stats-card transit-card">
                                    <div class="card-icon">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <div class="card-data">
                                        <h2>{{ $totalTransit }}</h2>
                                        <p>Transit</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="stats-card delivered-card">
                                    <div class="card-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="card-data">
                                        <h2>{{ $totalDelivered }}</h2>
                                        <p>Delivered</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="stats-card returned-card">
                                    <div class="card-icon">
                                        <i class="fas fa-undo"></i>
                                    </div>
                                    <div class="card-data">
                                        <h2>{{ $returnedCancel }}</h2>
                                        <p>Returned/Cancel</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-right mb-4">
                                <a href="{{ url('/').'/admin/bookings?start_date='.date('d/m/Y').'&end_date='.date('d/m/Y') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View All Today's Bookings
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Section with Responsive Layout -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-4">
                            <div class="chart-card h-100">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5>Top 5 Branches <span class="text-secondary small">{{ date(' (M Y)') }}</span></h5>
                                </div>
                                <div class="chart-container">
                                    {!! $branchChart->container() !!}
                                </div>
                                <div class="text-center py-2 mt-2">
                                    <a href="{{ url('/').'/admin/overview?type=branch' }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-chart-bar"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="chart-card h-100">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5>Top 5 Partners <span class="text-secondary small">{{ date(' (M Y)') }}</span></h5>
                                </div>
                                <div class="chart-container">
                                    {!! $partnerChart->container() !!}
                                </div>
                                <div class="text-center py-2 mt-2">
                                    <a href="{{ url('/').'/admin/overview?type=partner' }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-chart-bar"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="chart-card h-100">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5>Top 5 Customers <span class="text-secondary small">{{ date(' (M Y)') }}</span></h5>
                                </div>
                                <div class="chart-container">
                                    {!! $customerChart->container() !!}
                                </div>
                                <div class="text-center py-2 mt-2">
                                    <a href="{{ url('/').'/admin/overview?type=customer' }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-chart-bar"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Weekly Chart - Full Width -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="chart-card">
                                <h5 class="mb-3">Booking Weekly</h5>
                                <div class="chart-container weekly-chart">
                                    {!! $bookingByDateChart->container() !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links and Top Plans - Responsive Layout -->
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="chart-card">
                                <h5 class="mb-3">Quick Links</h5>
                                <div class="row quick-links">
                                    <div class="col-4 col-sm-4 mb-3">
                                        <a href="{{ url('/admin/bookings/create') }}" class="quick-link-card">
                                            <div class="icon-container">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <p>Single Booking</p>
                                        </a>
                                    </div>
                                    <div class="col-4 col-sm-4 mb-3">
                                        <a href="{{ url('/admin/bookings/bulk') }}" class="quick-link-card">
                                            <div class="icon-container">
                                                <i class="fas fa-boxes"></i>
                                            </div>
                                            <p>Bulk Booking</p>
                                        </a>
                                    </div>
                                    <div class="col-4 col-sm-4 mb-3">
                                        <a href="{{ url('/admin/tracking') }}" class="quick-link-card">
                                            <div class="icon-container">
                                                <i class="fas fa-search-location"></i>
                                            </div>
                                            <p>Track Consignment</p>
                                        </a>
                                    </div>
                                    <div class="col-4 col-sm-4 mb-3">
                                        <a href="{{ url('/admin/master/pincodes/create') }}" class="quick-link-card">
                                            <div class="icon-container">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <p>Add PIN</p>
                                        </a>
                                    </div>
                                    <div class="col-4 col-sm-4 mb-3">
                                        <a href="{{ url('/admin/master/branches/create') }}" class="quick-link-card">
                                            <div class="icon-container">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <p>Add Branch</p>
                                        </a>
                                    </div>
                                    <div class="col-4 col-sm-4 mb-3">
                                        <a href="{{ url('/admin/master/franchisees/create') }}" class="quick-link-card">
                                            <div class="icon-container">
                                                <i class="fas fa-handshake"></i>
                                            </div>
                                            <p>Add Partners</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="chart-card h-100">
                                <h5 class="mb-3">Top Plans</h5>
                                <div class="chart-container">
                                    {!! $subscriptionChart->container() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Load Chart Scripts -->
    {!! $branchChart->script() !!}
    {!! $customerChart->script() !!}
    {!! $bookingByDateChart->script() !!}
    {!! $partnerChart->script() !!}
    {!! $subscriptionChart->script() !!}

    <!-- Add this before the closing body tag to load font awesome if not already loaded -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
@endsection

<style>
    /* Dashboard Styling */
    .dashboard-container {
        padding: 0 15px;
    }

    /* Stats Cards Styling */
    .stats-row {
        margin-bottom: 1.5rem;
    }

    .stats-card {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        border-radius: 8px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        height: 100%;
        transition: transform 0.3s ease;
    }

    .main-container {
        flex: 1;
        margin-left: 220px !important;
        width: calc(100% - 200px);
        padding: 1rem;
    }

    .stats-card:hover {
        transform: translateY(-3px);
    }

    .bookings-card {
        background-color: #ff7e5f;
        background-image: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
        color: white;
    }

    .transit-card {
        background-color: #8c6ded;
        background-image: linear-gradient(135deg, #8c6ded 0%, #a892ee 100%);
        color: white;
    }

    .delivered-card {
        background-color: #34cac1;
        background-image: linear-gradient(135deg, #34cac1 0%, #5ce4d8 100%);
        color: white;
    }

    .returned-card {
        background-color: #e56de6;
        background-image: linear-gradient(135deg, #e56de6 0%, #f192f2 100%);
        color: white;
    }

    .card-icon {
        font-size: 2rem;
        margin-right: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.2);
    }

    .card-data {
        flex: 1;
    }

    .card-data h2 {
        font-size: 1.75rem;
        font-weight: bold;
        margin-bottom: 0.25rem;
    }

    .card-data p {
        font-size: 0.875rem;
        margin-bottom: 0;
        opacity: 0.9;
    }

    /* Chart Cards Styling */
    .chart-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 1.25rem;
        height: 100%;
    }

    .chart-container {
        height: 220px;
        position: relative;
    }

    .weekly-chart {
        height: 300px;
    }

    /* Quick Links Styling */
    .quick-links {
        margin: 0 -0.5rem;
    }

    .quick-link-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem 0.5rem;
        border-radius: 8px;
        background-color: #f8f9fa;
        color: #495057;
        text-decoration: none;
        text-align: center;
        height: 100%;
        transition: all 0.3s ease;
    }

    .quick-link-card:hover {
        background-color: #e9ecef;
        transform: translateY(-3px);
        text-decoration: none;
        color: #212529;
    }

    .icon-container {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: #6c757d;
    }

    .quick-link-card:hover .icon-container {
        color: #495057;
    }

    .quick-link-card p {
        font-size: 0.8rem;
        margin-bottom: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        .card-icon {
            width: 45px;
            height: 45px;
            font-size: 1.25rem;
        }

        .card-data h2 {
            font-size: 1.25rem;
        }

        .card-data p {
            font-size: 0.75rem;
        }

        .stats-card {
            padding: 0.75rem;
        }

        .chart-container {
            height: 180px;
        }

        .weekly-chart {
            height: 220px;
        }

        .welcome-message {
            font-size: 0.9rem;
            text-align: left;
            margin-top: 0.5rem;
        }
    }

    @media (max-width: 575.98px) {
        .quick-link-card p {
            font-size: 0.7rem;
        }

        .icon-container {
            font-size: 1.25rem;
        }
    }
</style>
