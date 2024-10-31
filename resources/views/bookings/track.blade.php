<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<link id="bsdp-css" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.standalone.css" rel="stylesheet">
@extends('layouts.app')

@section('auth-content')
        <div class="sb-page-header-content py-2">
            <div class="row">
                <div class="col-6">
                    <h3 class="text-left pt-3"><span>Track Bookings</span></h3>
                </div>
                <div class="col-6 text-right">
                    <div class="logo mb-4">
                        <img src="{{URL::to('/')}}/images/logo.png" />
                    </div>
                </div>
            </div>


            @if (session()->has('success'))
                <div class="alert alert-success">
                    {!! session()->get('success')!!}
                </div>
            @endif


            <div class="row">

                <div class="col-md-8">
                    <form class="form-inline" action="{{ route('bookings.track', request()->segment('2')) }}">
                        <div class="col-md-4 pl-0 pb-3">
                            <div class="form-group mb-0">
                                <input type="text" class="form-control" style="width: 100%" id="consg_num_search" name="consg_number" value="{{ request()->input('consg_number') }}" placeholder="Consignment Number">
                            </div>
                        </div>


                        <div class="col-md-4 pl-0 pb-3">
                            <select id="status" style="width: 100%" name="status" class="form-control @error('status') is-invalid @enderror">
                                <option class="form-control" selected disabled>Status</option>
                                @foreach($bookingStatuses as $status)
                                    <option value="{{ $status }}" {{ $status == request()->input('status') ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 pb-3">
                            <button type="submit" class="btn btn-primary mx-sm-2 mr-1 ">{{ 'Search' }}</button>
                            <a href="{{ route('bookings.track', request()->segment('2')) }}" class="btn mx-sm-1 btn-light btn-outline-secondary">{{ 'Reset' }}</a>
                        </div>


                    </form>
                </div>
            </div>

                <div class="col-md-4 text-right">
                        @if($bookings[0])
                            <h5 class="mb-0"><span>{{ 'Welcome '.$bookings[0]->customer_name}}</span></h5>
                        @endif
                            @if($bookings[0])
                                <div class="">{{ 'Booking date: '.date('d-m-Y', strtotime($bookings[0]->created_at)) }}</div>
                            @endif
                </div>


                <div class="table-responsive overflow-hidden col-12 mt-3">

                    <div class="card">
                        <table class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Consg. No.</th>
    {{--                                <th scope="col">Cust. Id</th>--}}
    {{--                                <th scope="col">Org. Pincode</th>--}}
                                    <th scope="col">Dest. Pincode</th>
                                    <th scope="col">Subscription</th>
    {{--                                <th scope="col">B Type</th>--}}
                                    <th scope="col">Status</th>
    {{--                                <th scope="col">B Date</th>--}}
    {{--                                <th scope="col">Operations</th>--}}
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                $index = $bookings->firstItem()
                            @endphp
                            @foreach($bookings as $booking)
                                <tr>
                                    <th scope="row">{{ $index++ }}</th>
                                    <td>{{ $booking->consg_number }}</td>
{{--                                    <td>{{ $booking->customer->code ?? '' }}</td>--}}
{{--                                    <td>{{ $booking->pincode->pincode ?? '' }}</td>--}}
                                    <td>{{ $booking->delivery->dest_pincode ?? '' }}</td>
                                    <td>{{ $booking->subs_name ?? '' }}</td>
{{--                                    <td>{{ $booking->batch_id ? 'Bulk': 'Single' }}</td>--}}
                                    <td>{{ $booking->status }}</td>
{{--                                    <td>{{ date('d-m-Y H:i', strtotime($booking->created_at)) }}</td>--}}
{{--                                    <td><button onclick="window.location.href ='{{ route('bookings.edit', $booking->id) }}'" class="btn btn-secondary btn-sm">Edit</button></td>--}}
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

        <style>

            th.tb_head {
                background: #181C93;
                color:white;
            }
        </style>


            <script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
            <script>
                $("#customer_id").on('change', function () {
                    $("#customer_id option:not(:last)").remove();
                });
                $('#customer_id').select2({
                    placeholder: "Choose Customer ID",
                    allowClear: true,
                    tags: true,
                    minimumInputLength: 3,
                    width: '180px',
                    // autocapitalize: on,
                    ajax: {
                        url: "{{ url('/admin/customer-search') }}",
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: $.trim(params.term)
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                        // cache: true
                    }
                });

                function officeType () {
                    return $("#office_type").val();
                }

                $('#office_id_search').select2({
                    placeholder: "Choose office ID",
                    width: '180px',
                    // minimumInputLength: 3,
                    ajax: {
                        url: "{{ url('admin/office-list') }}",
                        dataType: 'json',
                        data: function (params) {
                            return {
                                officeType: officeType(),
                                term: $.trim(params.term)
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                        // cache: true
                    }
                });

                $('#sandbox-container .input-daterange').datepicker({
                    format: "dd/mm/yyyy",
                    autoclose: true,
                    todayHighlight: true,
                    toggleActive: true,
                    endDate:'today'
                });
            </script>
@endsection
