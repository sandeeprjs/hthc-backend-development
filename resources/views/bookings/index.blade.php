<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<link href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" />

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="sb-page-header-content py-5">
        <div class="d-flex justify-content-between">
            <div>
                <h1 class="sb-page-header-title"><span>Bookings</span></h1>
            </div>
            <div class="dropdown">
                <a class="btn btn-primary dropdown-toggle" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ 'New Booking' }}
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="{{ route('bookings.create') }}">Single Booking</a>
                    <a class="dropdown-item" href="{{ route('bookings.bulk') }}">Bulk Booking</a>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
    <div class="alert alert-success">
        {!! session()->get('success') !!}
    </div>
    @endif

    <!-- Print Modal -->
    @if (session()->has('bulk-success'))
    <div class="modal fade" id="printConsignment" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    {!! session()->get('bulk-success') !!}
                    <p>Please click on <b>Print</b> to generate the consignment sheet.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button onclick="window.print()" class="btn btn-success">Print</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Filters -->
        <div class="col-12">
            <form class="row" action="{{ route('bookings.index') }}">
                <div class="col-md-3 pb-3">
                    <input type="text" class="form-control" name="consg_number" value="{{ request()->input('consg_number') }}" placeholder="Consignment Number">
                </div>

                <div class="col-md-3 pb-3">
                    <select id="customer_id" name="customer_id" class="form-control">
                        @if (request()->input('customer_id'))
                        <option value="{{ request()->input('customer_id') }}">{{ $customer->code }}</option>
                        @endif
                    </select>
                </div>

                <div class="col-md-3 pb-3">
                    <select id="fr_id" name="fr_id" class="form-control">
                        @if (request()->input('fr_id'))
                        <option value="{{ request()->input('fr_id') }}">{{ $franchisee->code }}</option>
                        @endif
                    </select>
                </div>

                <div class="col-md-3 pb-3">
                    <select name="status" class="form-control">
                        <option value="" disabled selected>Status</option>
                        @foreach ($bookingStatuses as $status)
                        <option value="{{ $status }}" {{ $status == request()->input('status') ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 pb-3">
                    <select name="subscription_id" class="form-control">
                        <option value="" disabled selected>Select a Plan</option>
                        @foreach ($subscriptions as $subscription)
                        <option value="{{ $subscription->id }}" {{ $subscription->id == request()->input('subscription_id') ? 'selected' : '' }}>
                            {{ $subscription->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 pb-3">
                    <div class="input-daterange input-group">
                        <input type="text" class="form-control" name="start_date" placeholder="From Date" value="{{ request()->input('start_date') }}">
                        <input type="text" class="form-control" name="end_date" placeholder="To Date" value="{{ request()->input('end_date') }}">
                    </div>
                </div>

                <div class="col-md-3 pb-3">
                    <button type="submit" class="btn btn-primary" name="btnSubmit" value="search">Search</button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-light">Reset</a>
                    <button type="submit" class="btn btn-primary" name="btnSubmit" value="export">Export</button>
                </div>
            </form>
        </div>

        <!-- Booking Table -->
        <div class="table-responsive col-12 mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Consg. No.</th>
                        <th>Cust. Id</th>
                        <th>Pincode</th>
                        <th>Subscription</th>
                        <th>B Type</th>
                        <th>B Date</th>
                        <th>Status</th>
                        <th>Emp Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $index = $bookings->firstItem() @endphp
                    @foreach ($bookings as $booking)
                    <tr>
                        <td>{{ $index++ }}</td>
                        <td>{{ $booking->consg_number }}</td>
                        <td>{{ $booking->customer->code ?? '' }}</td>
                        <td>{{ $booking->pincode->pincode ?? '' }}</td>
                        <td>{{ $booking->subs_name ?? '' }}</td>
                        <td>{{ $booking->batch_id ? 'Bulk' : 'Single' }}</td>
                        <td>{{ date('d-m-Y H:i', strtotime($booking->created_at)) }}</td>
                        <td>{{ is_array($booking->status) ? implode(', ', array_unique($booking->status)) : $booking->status }}</td>
                        <td>{{ $booking->user->username ?? '' }}</td>
                        <td>
                            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="{{ route('bookings.view', $booking->id) }}" class="btn btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $bookings->appends(request()->query())->links() }}
        </div>
    </div>
</div>
<style>
    /* Fix for date range inputs */
    .input-daterange.input-group {
        display: flex !important;
        flex-wrap: nowrap !important;
        width: 100% !important;
    }

    .input-daterange.input-group input {
        width: 50% !important;
        border-radius: 4px !important;
        margin-right: 5px !important;
    }

    .input-daterange.input-group input:last-child {
        margin-right: 0 !important;
    }

    /* Filter row fix - makes filters display horizontally */
    .container form.row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -10px;
        margin-left: -10px;
    }

    .container form.row > div {
        padding-right: 10px;
        padding-left: 10px;
        margin-bottom: 15px;
    }

    /* Fix button alignment */
    .container form.row > div:last-child {
        display: flex;
        align-items: flex-start;
    }

    .container form.row > div:last-child .btn {
        margin-right: 8px;
    }

    /* Make filters responsive */
    @media (min-width: 992px) {
        .container form.row > div {
            flex: 0 0 25%;
            max-width: 25%;
        }

        .container form.row > div:nth-last-child(2) {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
    }

    @media (min-width: 768px) and (max-width: 991px) {
        .container form.row > div {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
    }

    @media (max-width: 767px) {
        .container form.row > div {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .container form.row > div:last-child {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    @media (max-width: 575px) {
        .container form.row > div {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>

<script>
    // Make sure jQuery is loaded before this script
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize datepicker directly on each input
        try {
            if($.fn.datepicker) {
                $('input[name="start_date"], input[name="end_date"]').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true
                });

                // Also try initializing as a range
                $('.input-daterange').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true
                });
            } else {
                console.error('Datepicker plugin not found');
            }
        } catch(e) {
            console.error('Error initializing datepicker:', e);
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
<script>
    $(function () {
        // Initialize Select2
        $('#customer_id, #fr_id').select2({
            placeholder: "Search",
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: function () {
                    return $(this).attr('id') === 'customer_id' ? "{{ url('/admin/customer-search') }}" : "{{ url('admin/office-list') }}";
                },
                dataType: 'json',
                processResults: function (data) {
                    return { results: data };
                }
            }
        });

        // Datepicker
        $('.input-daterange').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // Print Modal
        $('#printConsignment').modal('show');
    });
</script>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        padding: 0;
        margin: 1rem 0;
        list-style: none;
    }

    .pagination li {
        display: inline-block;
        margin: 0 5px;
    }

    .pagination li a,
    .pagination li span {
        display: inline-block;
        padding: 5px 10px;
        color: #007bff;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
    }

    .pagination li a:hover {
        color: white;
        background-color: #007bff;
        border-color: #007bff;
    }

    .pagination .active span {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination .disabled span {
        color: #6c757d;
        pointer-events: none;
    }

    .pagination svg {
        width: 10px;
        height: 10px;
        vertical-align: middle;
    }
</style>

@endsection
