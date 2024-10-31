<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<link id="bsdp-css" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.standalone.css" rel="stylesheet">
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Bookings</span></h1>
                </div>
                <div class="dropdown">
                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ 'New Booking' }}</a>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('bookings.create') }}">Single Booking</a>
                        <a class="dropdown-item" href="{{ route('bookings.bulk') }}">Bulk Booking</a>
                    </div>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success">
                {!! session()->get('success')!!}
            </div>
        @endif

        <!--Print Modal -->
        @if (session()->has('bulk-success'))
            <div class="modal fade" id="printConsignment" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            {!! session()->get('bulk-success') !!}
                            Please click on <b>print</b> button to generate the consignment sheet.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                            <button onclick="window.print()" class="btn btn-success noprint">Print</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row ">
                <div class="col-12">
                    <div class="row booking-filter">
                        <form class="form-inline" action="{{ route('bookings.index') }}">
                            <div class="col-md-3 pb-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="consg_num_search" name="consg_number" value="{{ request()->input('consg_number') }}" placeholder="Consignment Number">
                                </div>
                            </div>

                            <div class="col-md-3 pb-3">
                                {{--                    <label>{{'Customer ID'}}<span class="text-danger">*</span></label>--}}
                                <select id="customer_id" name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                                    @if( request()->input('customer_id') )
                                        <option value="{{ request()->input('customer_id') }}">{{ $customer->code }}</option>
                                    @endif
                                </select>
                                @error('customer_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-3 pb-3">
                                {{--                    <label>{{'Customer ID'}}<span class="text-danger">*</span></label>--}}
                                <select id="fr_id" name="fr_id" class="form-control @error('fr_id') is-invalid @enderror">
                                    @if( request()->input('fr_id') )
                                        <option value="{{ request()->input('fr_id') }}">{{ $franchisee->code }}</option>
                                    @endif
                                </select>
                                @error('fr_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-3 pb-3">
                                <select id="status" style="width: 100%" name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option class="form-control" selected disabled>Status</option>
                                    @foreach($bookingStatuses as $status)
                                        <option value="{{ $status }}" {{ $status == request()->input('status') ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="subscription_id" name="subscription_id"  class="form-control @error('subscription_id') is-invalid @enderror">
                                    <option class="form-control" selected disabled>Select a Plan</option>
                                    @foreach($subscriptions as $subscription)
                                        <option value="{{ $subscription->id }}" {{ $subscription->id == request()->input('subscription_id') ? 'selected' : '' }}>{{ $subscription->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="sandbox-container">
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input type="text" class="input-sm form-control" name="start_date" placeholder="From Date" value="{{ request()->input('start_date') }}">

                                        <input type="text" class="input-sm form-control" name="end_date" placeholder="To Date" value="{{ request()->input('end_date') }}">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mx-sm-2 " name="btnSubmit" value="search">{{ 'Search' }}</button>
                            <a href="{{ route('bookings.index') }}" class="btn  mx-sm-1 btn-light btn-outline-secondary">{{ 'Reset' }}</a>
                            <button type="submit" class="btn btn-primary mx-sm-2 " name="btnSubmit" value="export">{{ 'Export' }}</button>

                       </form>


                    </div>
                </div>

            <div class="table-responsive col-12 mt-3">
                <div class="card">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Consg. No.</th>
                            <th scope="col">Cust. Id</th>
                            <th scope="col">Pincode</th>
                            <!-- <th scope="col">Dest. Pincode</th> -->
                            <th scope="col">Subscription</th>
                            <th scope="col">B Type</th>
                            <th scope="col">B Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Emp Code</th>
                            <th scope="col">Operations</th>
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
                                <td> {{ $booking->customer->code ?? '' }} </td>
                                <td> Orig - {{ $booking->pincode->pincode ?? '' }}
                                    <br>
                                     Dest - {{ $booking->delivery->dest_pincode ?? '' }}
                                </td>
                                <!-- <td>{{ $booking->pincode->pincode ?? '' }}</td> -->
                                <!-- <td>{{ $booking->delivery->dest_pincode ?? '' }}</td> -->
                                <td>{{ $booking->subs_name ?? '' }}</td>
                                <td>{{ $booking->batch_id ? 'Bulk': 'Single' }}</td>
                                <td>{{ date('d-m-Y H:i', strtotime($booking->created_at)) }}</td>
                                <td style="max-width:90px;">
                                    {{ $booking->status}}
                                </td>
                                <td> @if(isset($booking->user->username))
                                          {{ $booking->user->username }} <br>
                                          {{ $booking->user->first_name }} {{ $booking->user->last_name }}
                                     @endif
                                </td>
                                <td><button onclick="window.location.href ='{{ route('bookings.edit', $booking->id) }}'" class="btn btn-secondary btn-sm">Edit</button>
                                <button onclick="window.location.href ='{{ route('bookings.view', $booking->id) }}'" class="btn btn-secondary btn-sm">View</button>
                               
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {!! $bookings->appends(request()->query())->links() !!}
            </div>

            @if(session()->has('batch_id'))
                <style>
                    *{
                        margin:0;
                        padding:0
                    }
                </style>
                <body class="padding: 0px; margin: 0px;">
                <div id="printArea" style="page-break-inside: avoid;">
                    <div class="container1" style="page-break-inside: avoid;">
                        <div class="no-break">
                                @php
                                    $deliveries = session()->get('deliveries');
                                    $customer = session()->get('customer');
                                @endphp
                                @foreach($deliveries as $delivery)
                                    <div class="column" style="page-break-inside: avoid; height:300px; padding-bottom: 30px; padding-top: 30px; padding-left: 30px; ">
                                        <div class="namecard" style="line-height: 16px; text-align: left; padding-top: 40px; padding-left: 40px; padding-right: 80px; ">
                                           <h4 style="height: 170px">{{ $delivery->receiver_name }}<br>
                                               {{ $delivery->mobile_number }}<br>
                                               {{ $delivery->add_line_1 }}
                                               {{ $delivery->add_line_2 }}<br>
                                               {{ $delivery->city }}<br>
                                           </h4>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-left" style="padding-left: 60px">
                                                <div style="text-align: left; font-size: 18px; font-weight: bold " text-anchor= "middle" >{{ $delivery['pincode']->pincode }}</div>
                                                <img src="data:image/png;base64, {{ \Milon\Barcode\DNS1D::getBarcodePNG($delivery['booking']->consg_number, "C128",1.8,45,array(1,1,1)) }}" alt="barcode"/>
                                                <div style="text-align: left; font-size: 18px; font-weight: bold " text-anchor= "middle" >{{ $delivery['booking']->consg_number }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </body>
            @endif
    </div>

    <style>
        #printArea {
            display: none;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #printArea * {
                visibility: visible;
            }
            #printArea {
                display: block;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-break {
                page-break-inside: avoid;
            }

            .column {
                float: left;
                width: 50%;
                text-align: center;
            }

            /* Clearfix (clear floats) */


            /*@page{*/
            /*    size: A4;*/
            /*    margin: 2mm ;*/
            /*    text-align: center;*/
            /*    orphans: 0!important;*/
            /*    widows: 0!important;*/
            /*}*/
        }
    </style>
    <script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        $(window).on('load', function () {
            $('#printConsignment').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            })
        });

        $("#customer_id").on('change', function () {
            $("#customer_id option:not(:last)").remove();
        });
        $('#customer_id').select2({
            placeholder: "Choose Customer ID",
            allowClear: true,
            minimumInputLength: 2,
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

        $("#fr_id").on('change', function () {
            $("#fr_id option:not(:last)").remove();
        });
        $('#fr_id').select2({
            placeholder: "Choose Partner ID",
            allowClear: true,
            minimumInputLength: 2,
            width: '180px',
            // autocapitalize: on,
            ajax: {
                url: "{{ url('admin/office-list') }}",
                dataType: 'json',
                data: function (params) {
                    return {
                        officeType: 'FR',
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
