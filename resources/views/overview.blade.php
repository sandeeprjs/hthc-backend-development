<link id="bsdp-css" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.standalone.css" rel="stylesheet">
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span class="text-capitalize">Top {{ request()->input('type') }}</span> <span>{{ request()->input('month_year') ? \Carbon\Carbon::createFromFormat('m/Y', request()->input('month_year'))->format('F Y') : date('F Y') }}</span></h1>
                </div>
            </div>
        </div>

        <div class="row ">
            <form class="form-inline" action="{{ route('overview') }}">

                <div class="form-group " id="sandbox-container">
                    <input type="hidden" class="input-sm form-control" name="type" value="{{ request()->input('type') }}">
                    <div class="input-daterange input-group pl-3" id="datepicker">
                        <input type="text" class="input-sm form-control" name="month_year" placeholder="Month and Year" value="{{ request()->input('month_year') }}">
                    </div>

                </div>

                <button type="submit" class="btn btn-primary mx-sm-2 ">{{ 'Search' }}</button>
                <a href="{{ route('overview').'?type='.request()->input('type') }}" class="btn  mx-sm-1 btn-light btn-outline-secondary">{{ 'Reset' }}</a>
            </form>

            <div class="table-responsive col-12 mt-3">
                <div class="card">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col"><span class="text-capitalize">{{ request()->input('type') }}</span> Name</th>
                            <th scope="col"><span class="text-capitalize">{{ request()->input('type') }}</span> Code</th>
                            <th scope="col">Total Bookings</th>
{{--                            <th scope="col">Org. Pincode</th>--}}
{{--                            <th scope="col">Dest. Pincode</th>--}}
{{--                            <th scope="col">Subscription</th>--}}
{{--                            <th scope="col">B Type</th>--}}
{{--                            <th scope="col">B Date</th>--}}
{{--                            <th scope="col">Status</th>--}}
{{--                            <th scope="col">Operations</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $index = $bookings->firstItem()
                        @endphp
                        @foreach($bookings as $booking)
                            @php
                                $booking = $booking->toArray();
                            @endphp
                            <tr>
                                <th scope="row">{{ $index++ }}</th>
                                @if(request()->input('type') == 'customer')
                                    <td>{{ $booking['customer_name'] }}</td>
                                    <td>{{ $booking['customer']['code'] ?? '' }}</td>
                                    <td>{{ $booking['total'] }}</td>
                                @elseif(request()->input('type') == 'branch')
                                    <td>{{ $booking['booking_branch']['branch_name'] ?? '' }}</td>
                                    <td>{{ $booking['booking_branch']['code'] ?? '' }}</td>
                                    <td>{{ $booking['total'] }}</td>
                                @else
                                    <td>{{ $booking['booking_franchisee']['enterprise_name'] ?? '' }}</td>
                                    <td>{{ $booking['booking_franchisee']['code'] ?? '' }}</td>
                                    <td>{{ $booking['total'] }}</td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(count($bookings) == 0)
                        <div class="h5 p-4 text-center">No record found!</div>
                    @endif
                </div>
                {!! $bookings->appends(request()->query())->links() !!}
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('#sandbox-container .input-daterange').datepicker({
            format: "mm/yyyy",
            initialDate: new Date(),
            autoclose: true,
            // todayHighlight: true,
            // toggleActive: true,
            endDate:'today',
            startView: 'months',
            minViewMode: 'months',
        });
    </script>
@endsection
