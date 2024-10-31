<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@extends('layouts.app')

@section('content')
    <div class="container">
    <div class="sb-page-header-content py-5">
        <div class="d-flex justify-content-between">
            <div>
                <h1 class="sb-page-header-title"><span>Validate Excel Data</span></h1>
            </div>
        </div>


        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->input('has_error') == 0 ? 'active' : '' }}" href="{{ route('bookings.validate', [\Illuminate\Support\Facades\Request::segment(4), 'has_error=0']) }}">Accurate Records</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ request()->input('has_error') == 1 ? 'active' : '' }}" href="{{ route('bookings.validate', [\Illuminate\Support\Facades\Request::segment(4), 'has_error=1']) }}">Inaccurate Records</a>
            </li>
        </ul>

        <div class="table-responsive col-12 mt-3">
            <div class="card">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Area</th>
                        <th scope="col">Pincode</th>
                        <th scope="col">City</th>
                        <th scope="col">Mobile No.</th>
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
                            <td>{{ $booking->receiver_name }}</td>
                            <td>{{ $booking->receiver_add_line_1 ?? '' }}</td>
                            <td>{{ $booking->receiver_add_line_2 ?? '' }}</td>
                            <td {{ $booking->has_error == 1 ?'class=text-danger':'' }}>{{ $booking->pincode->pincode ?? $booking->wrong_pincode }}</td>
                            <td>{{ $booking->receiver_city ?? '' }}</td>
                            <td>{{ $booking->receiver_mobile_number }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" id="{{ $booking->id }}" class="btn btn-primary btn-sm edit-data" data-toggle="modal" data-target=".bd-example-modal-lg">Edit</button>
                                    <button type="button" id="{{ $booking->id }}" class="btn btn-danger btn-sm delete-data" data-toggle="modal" data-target="#deleteConfirm">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($bookings) == 0)
                    <div class="p-4 text-center">
                        <span class="h5">No {{ request()->input('has_error') == 0 ? 'accurate': 'inaccurate' }} record found!</span>
                        @if( request()->input('has_error') == 1 )
                            <a class="nav-link" href="{{ route('bookings.validate', [\Illuminate\Support\Facades\Request::segment(4), 'has_error=0']) }}">Go to Accurate Records</a>
                        @endif
                    </div>
                @endif
            </div>
            {!! $bookings->appends(request()->query())->links() !!}
        </div>

{{--        Edit Model--}}
        <div id="editDataModal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Record</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="edit_bulk_booking" method="post">
                            @method('PUT')
                            {{csrf_field()}}
                            <input type="hidden" id="bulk_booking_id" name="bulk_booking_id" value="">
                            <div class="form-group col">
                                <label>Name</label>
                                <input type="text" name="receiver_name" id="receiver_name" class="form-control" value="">
                            </div>
                            <div class="form-group col">
                                <label>Address</label>
                                <textarea type="text" name="receiver_address" id="receiver_address" class="form-control" value=""></textarea>
                            </div>
                            <div class="form-group col">
                                <label>Area</label>
                                <input type="text" name="receiver_area" id="receiver_area" class="form-control" value="">
                            </div>
                            <div class="form-group pt-3 pl-3 pr-3 row">
                                <div id="wrong_pincode_div" class="col">

                                    <label for="wrong_pincode">Wrong Pincode</label> <br/>
                                    <input type="text" readonly name="wrong_pincode" id="wrong_pincode" class="form-control" value="">
                                </div>

                                <div class="col">
                                    <label for="receiver_pincode_id">{{ 'Pincode' }}<span class="text-danger">*</span></label> <br/>
                                    <select id="receiver_pincode_id" name="receiver_pincode_id" required class="form-control @error('receiver_pincode_id') is-invalid @enderror">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col">
                                <label>Mobile</label>
                                <input type="text" name="receiver_mobile" id="receiver_mobile" class="form-control " value="">
                            </div>
{{--                            <input type="submit" class="btn btn-primary btn-ok" value="{{ 'Update' }}" />--}}
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="">Close</button>
                        <button form="edit_bulk_booking" type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- delete modal -->
        <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        {{ "Please confirm to delete this booking" }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                        <form id="delete_bulk_booking" method="POST" style="display:inline">
                            @method('DELETE')
                            {{ csrf_field() }}
                            <input type="hidden" id="deleteId" name="deleteId">
                            <input type="submit" class="btn btn-danger btn-ok" value="{{ 'Confirm' }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
{{--        <div class="col">--}}
{{--            <label for="receiver_pincode_id">{{ 'Pincode' }}<span class="text-danger">*</span></label>--}}
{{--            <select id="receiver_pincode_id" name="receiver_pincode_id" required class="form-control @error('receiver_pincode_id') is-invalid @enderror">--}}
{{--            </select>--}}
{{--        </div>--}}

        <div class="text-center p-4">
{{--            <button type="button" id="saveRecord" name="saveRecord" class="btn btn-success">{{ 'Continue with accurate records*' }}</button>--}}

            <a class="btn btn-success {{ count($bookings) == 0 ? 'disabled': ''}}" href="{{ count($bookings) == 0 ? '#' : route('bulk-booking.create', $batchId) }}">{{ 'Continue with accurate records*' }}</a>
            <small id="saveRecordHelp" class="form-text text-muted">*All records with errors will be ignored in this operation</small>
        </div>

    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        $(".edit-data").on('click', function () {
            let id = this.id;
            $.ajax({
                url: "{{ url('/admin/bookings/bulk-booking-details') }}",
                method: 'get',
                data: {
                    id: id,
                },
                success: function (result) {
                    $("#bulk_booking_id").val(id);
                    $("#receiver_name").val(result.receiver_name);
                    $("#receiver_address").val(result.receiver_add_line_1);
                    $("#receiver_area").val(result.receiver_add_line_2);
                    $("#receiver_mobile").val(result.receiver_mobile_number);
                    $("#wrong_pincode").val(result.wrong_pincode);
                    if ($("#wrong_pincode").val() === '') {
                        $("#wrong_pincode_div").hide();
                    }
                    $("#receiver_pincode_id").append("<option value='"+result.receiver_pincode_id+"'>"+result.pincode.pincode  +"</option>");
                }
            })
        });

        $('#receiver_pincode_id').select2({
            placeholder: "Choose Correct Pincode",
            width: "200px",
            dropdownParent: $('#editDataModal'),
            ajax: {
                url: "{{ url('/admin/branch/find') }}",
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
                cache: true
            }
        });

        $("#edit_bulk_booking").on('submit', function (event) {
            event.preventDefault();
            let form_data = $(this).serialize();
            $.ajax({
                url: "{{ route('bookings.row-update') }}",
                method: "PUT",
                data: form_data,
                dataType: "json",
                success: function () {
                    alert('Record updated');
                    $("#editDataModal").modal('hide');
                    window.location.replace(window.location.href);
                },
                error: function () {
                    alert('unable to update')
                }
            })
        });

        $(".delete-data").on('click', function () {
            let id = this.id;
            $("#deleteId").val(id);
        });

        $("#delete_bulk_booking").on('submit', function (event) {
            event.preventDefault();
            let fromData = $(this).serialize();
            console.log(FormData);
            $.ajax({
                url: "{{ route('bookings.row-delete') }}",
                method: "DELETE",
                data: fromData,
                dataType: "json",
                success: function () {
                    alert('Record removed');
                    window.location.replace(window.location.href);
                },
                error: function () {
                    alert('Can\'t remove this record')
                }

            })
        });

    </script>
@endsection
