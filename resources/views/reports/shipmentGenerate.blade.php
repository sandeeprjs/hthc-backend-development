<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
<link id="bsdp-css" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.standalone.css"
      rel="stylesheet">
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Shipments Report</span></h1>
                </div>

            </div>
        </div>
    </div>


    <div class="row m-0 partner-report">
        <div class="col-12">
            <div class="container">
                <form class="form-inline" action="{{ route('shipment.report') }}">
                    <div class="row">
                    <div class="col-4">
                        <div class="form-group" id="sandbox-container">
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control" name="start_date"
                                       placeholder="From Date" value="{{ request()->input('start_date') }}">
                                <input type="text" class="input-sm form-control " name="end_date"
                                       placeholder="To Date" value="{{ request()->input('end_date') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <select id="customer_id" name="customer_id"
                                class="form-control @error('customer_id') is-invalid @enderror">
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

                    <div class="col">
                        <select id="status" style="width: 100%" name="status"
                                class="form-control @error('status') is-invalid @enderror">
                            <option class="form-control" selected disabled>Status</option>
                            @foreach($bookingStatuses as $status)
                                <option
                                    value="{{ $status }}" {{ $status == request()->input('status') ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <select id="subscription_id" name="subscription_id"
                                class="form-control @error('subscription_id') is-invalid @enderror">
                            <option class="form-control" selected disabled>Select a Plan</option>
                            @foreach($subscriptions as $subscription)
                                <option
                                    value="{{ $subscription->id }}" {{ $subscription->id == request()->input('subscription_id') ? 'selected' : '' }}>{{ $subscription->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <select id="consg_type" name="consg_type"
                                class="form-control @error('subscription_id') is-invalid @enderror">
                            <option class="form-control" selected disabled>Select Type</option>
                            <option value="dox"> Dox</option>
                            <option value="non-dox"> Non Dox</option>
                        </select>
                    </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col pl-2">
                            <button type="submit" class="btn btn-primary mx-sm-2 " name="btnSubmit"
                                    value="fetch">{{ 'Fetch Report' }}</button>
                            <a href="{{ route('shipment.report') }}"
                               class="btn  mx-sm-1 btn-light btn-outline-secondary">{{ 'Reset' }}</a>
                            <button type="submit" class="btn btn-primary mx-sm-2 " name="btnSubmit"
                                    value="export">{{ 'Export' }}</button>
                        </div>
                    </div>

                </form>
            </div>



        </div>
        @if($bookings)
            <div class="table-responsive col-12 mt-3">
                <div class="card">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Consg. No.</th>
                            <th scope="col">Cust. Id</th>
                           
                            <th scope="col">Dest. Pincode</th>
                            <th scope="col">Subscription</th>
                            <th scope="col">B Type</th>
                            <th scope="col">Delivery / Return Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Emp Code</th>
                            <th scope="col">Weight (g)</th>

                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $index = $bookings->firstItem()
                        @endphp
                        @if($index == '')
                            <tr>
                                <td colspan="10"> No Records Found</td>
                            </tr>
                        @endif
                        @foreach($bookings as $booking)
                            <tr>
                                <th scope="row">{{ $index++ }}</th>
                                <td>{{ $booking->consg_number }}</td>
                                <td>{{ $booking->customer->code ?? '' }}</td>
                               
                                <td>{{ $booking->delivery->dest_pincode ?? '' }}</td>
                                <td>{{ $booking->subs_name ?? '' }}</td>
                                <td>{{ $booking->batch_id ? 'Bulk': 'Single' }}</td>
                                <td>
                                    @if($booking->status == 'Delivered' || $booking->status == 'Returned')
                                            {{ date('d-m-Y H:i', strtotime($booking->delivery->updated_at)) }}
                                    @else
                                            {{ date('d-m-Y H:i', strtotime($booking->created_at)) }}
                                    @endif
                                </td>
                                <td>{{ $booking->status }}</td>
                                <td>{{ $booking->delivery->user['username'] }}   </td>
                                <td>
                                    @if($booking->final_weight != '')
                                        {{ $booking->final_weight }}
                                    @else
                                        {{ $booking->weight }}
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        @endif

    </div>
    </div>




    <style>
        input.input-sm.form-control.to_date_cls {
            margin-left: 53px;
        }

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

        function officeType() {
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
            endDate: 'today'
        });
    </script>
@endsection
