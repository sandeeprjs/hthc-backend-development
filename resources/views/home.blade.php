@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row pt-4">
            <div class="col-md-12">


                <main>
                    @php
                        $user = \Illuminate\Support\Facades\Auth::user();
                    @endphp


                    {{--                <main>--}}
                    {{--                    <div class="sb-page-header">--}}
                    {{--                        <div class="container-fluid text-center">--}}
                    {{--                            <div class="d-flex justify-content-center align-items-center">--}}
                    {{--                                <div class="dash-block">--}}
                    {{--
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}

                    {{--                </main>--}}


                    <div class="dashboard-header-block">

                        <div class="row pb-4 pt-2">
                            <div class="col-6">
                                <h5>Today's Booking</h5>
                            </div>
                            <div class="col-6 text-right">
                                <p> @foreach($user->roles as $role)
                                        @if($role->id == 1)
                                            {{ 'Welcome Administrator' }}
                                        @else
                                            {{ 'Welcome '.$user->first_name. ' '.$user->last_name }}
                                        @endif
                                    @endforeach
                                </p>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="dash-block-card" style="background: #fb875d">
                                    <h2>{{ $bookingsToday }}</h2>
                                    <p>Bookings</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="dash-block-card" style="background: #8c6ded">
                                    <h2>{{ $totalTransit }}</h2>
                                    <p>Transit</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="dash-block-card" style="background: #34cac1">
                                    <h2>{{ $totalDelivered }}</h2>
                                    <p>Delivered</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="dash-block-card" style="background: #e56de6">
                                    <h2>{{ $returnedCancel }}</h2>
                                    <p>Returned/Cancel</p>
                                </div>
                                <a href="{{ url('/').'/admin/bookings?start_date='.date('d/m/Y').'&end_date='.date('d/m/Y') }}" class=""> Show More... </a>
                            </div>

                        </div>

                    </div>

                    <div class="dashboard-chart-container">
                        <div class="row">
                            <div class="col-4">
                                <div class="dashboard-cart">
                                    <div class="row">
                                        <div class="col-9">
                                            <h5>Top 5 Branches<span class="text-secondary small">{{ date(' (M Y)') }}</span></h5>
                                        </div>
                                    </div>
                                    <div>
                                        {!! $branchChart->container() !!}
                                    </div>
                                    <div class="text-center py-3">
                                        <a href="{{ url('/').'/admin/overview?type=branch' }}" class=""> Show More... </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="dashboard-cart">
                                    <div class="row">
                                        <div class="col-9">
                                            <h5>Top 5 Partners<span class="text-secondary small">{{ date(' (M Y)') }}</span></h5>
                                        </div>
                                    </div>
                                    <div>
                                        {!! $partnerChart->container() !!}
                                    </div>
                                    <div class="text-center py-3">
                                        <a href="{{ url('/').'/admin/overview?type=partner' }}" class=""> Show More... </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="dashboard-cart">
                                    <div class="row">
                                        <div class="col-9">
                                            <h5>Top 5 Customer<span class="text-secondary small">{{ date(' (M Y)') }}</span></h5>
                                        </div>
                                    </div>
                                    <div>
                                        {!! $customerChart->container() !!}
                                    </div>
                                    <div class="text-center py-3">
                                        <a href="{{ url('/').'/admin/overview?type=customer' }}" class=""> Show More... </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="dash-block-bottom">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="dashboard-cart">
                                    <h5>Booking Weekly</h5>
                                    {!! $bookingByDateChart->container() !!}
                                </div>
                            </div>
{{--                            <div class="col-md-12">--}}
{{--
{{--                            </div>--}}
                        </div>
                    </div>


                    <div class="dash-block-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="dashboard-cart back-dis">
                                    <h5>Quick Links</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="link">
                                                <a href="{{ url('/admin/bookings/create') }}">
                                                    <span> <img src="{{ url('/images/booking1.png') }}" /></span>
                                                    <p>Single Booking</p>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="link">
                                                <a href="{{ url('/admin/bookings/bulk') }}">
                                                    <span> <img src="{{ url('/images/pallet.png') }}" /></span>
                                                    <p>Bulk Booking</p>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="link">
                                                <a href="{{ url('/admin/tracking') }}">
                                                    <span> <img src="{{ url('/images/tracking1.png') }}" /></span>
                                                    <p>Track Consignment</p>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="link">
                                                <a href="{{ url('/admin/master/pincodes/create') }}">
                                                    <span> <img src="{{ url('/images/marker.png') }}" /></span>
                                                    <p>Add PIN</p>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="link">
                                                <a href="{{ url('/admin/master/branches/create') }}">
                                                    <span> <img src="{{ url('/images/warehouse.png') }}" /></span>
                                                    <p>Add Branch</p>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="link">
                                                <a href="{{ url('/admin/master/franchisees/create') }}">
                                                    <span> <img src="{{ url('/images/agreement.png') }}" /></span>
                                                    <p>Add Partners</p>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="dashboard-cart">
                                    <h5>Top Plans</h5>
                                    {!! $subscriptionChart->container() !!}
                                </div>
                            </div>
                        </div>
                    </div>


                </main>
            </div>
        </div>
    </div>
    {!! $branchChart->script() !!}
    {!! $customerChart->script() !!}
    {!! $bookingByDateChart->script() !!}
    {!! $partnerChart->script() !!}
    {!! $subscriptionChart->script() !!}
@endsection
