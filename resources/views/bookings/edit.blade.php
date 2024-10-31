<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Edit Booking</span></h1>
                </div>
                {{--                <div>--}}
                {{--                    <a class="btn btn-primary" href="{{ route('branches.index') }}"><i class="fa fa-plus-circle"></i> View Branches</a>--}}
                {{--                </div>--}}
            </div>
        </div>

{{--        @if ($errors->any())--}}
{{--            <div class="alert alert-danger">--}}
{{--                <strong>Whoops!</strong> There were some problems with your input.<br><br>--}}
{{--                <ul>--}}
{{--                    @foreach ($errors->all() as $error)--}}
{{--                        <li>{{ $error }}</li>--}}
{{--                    @endforeach--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        @endif--}}
        <form id="editBooking">
            @method('PUT')
            {{ csrf_field() }}

            <div class="col-8 mx-auto form-row mb-4">
                <div class="form-group col-md-4">
                    <label>{{'Consignment Number'}}<span class="text-danger">*</span></label>
                    <input type="text" name="consg_number" required class="form-control @error('consg_number') is-invalid @enderror" value="{{ old('consg_number') ?? $booking->consg_number }}">
                    @error('consg_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="consgType">{{ 'Consignment Type' }}<span class="text-danger">*</span></label>
                    <select id="consgType" class="form-control" name="consg_type" onkeyup="this.value = this.value.toUpperCase();">
                        <option value="" disabled selected class="form-control">Select a type</option>
                        <option {{ $booking->consg_type == 'dox'? 'selected' : ''}} value="dox">{{ 'Dox' }}</option>
                        <option {{ $booking->consg_type == 'non-dox'? 'selected' : ''}} value="non-dox">{{ 'Non-Dox' }}</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="subscription_id">{{ 'Plans' }}<span class="text-danger">*</span></label>
                    <select id="subscription_id" class="form-control" name="subscription_id">
                        <option value="" disabled selected class="form-control">Select a plan</option>
                        @foreach($subscriptionLists as $subscription)
                            {!! $subscription !!}
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-10 mx-auto">
                    <div class="row">
                        <div class="col-6">
                            <div class="booking-card">
                                <div class="d-flex justify-content-between">
                                    <h4>{{ 'From Address' }}</h4>
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="sender_sms" {{ $booking->sms_to_sender == 1 ? 'checked' : '' }} value="1" class="form-check-input @error('sender_sms') is-invalid @enderror" id="sender_sms" value="{{ old('sender_sms') }}">
                                        <label class="form-check-label" for="sender_sms">{{'SMS Notification'}}</label>
                                        @error('sender_sms')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{'Customer ID'}}<span class="text-danger">*</span></label>
                                        <select id="customer_id" required name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                                            @if($booking->customer)
                                                <option value="{{ $booking->customer_id }}">{{ $booking->customer->code }}</option>
                                            @endif
                                        </select>
                                        @error('customer_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{'Customer Name'}}<span class="text-danger">*</span></label>
                                        <input type="text" id="sender_name" required name="sender_name" class="form-control @error('sender_name') is-invalid @enderror" value="{{ old('sender_name') ?? $booking->customer_name }}">
                                        @error('sender_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{'Address'}}</label>
                                    <textarea type="textarea" id="sender_address" name="sender_address" class="form-control @error('sender_address') is-invalid @enderror" >{{ old('sender_address') ?? $booking->add_line_1 }}</textarea>
                                    @error('sender_address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{'Area'}}</label>
                                        <input type="text" id="sender_area" name="sender_area" class="form-control @error('sender_area') is-invalid @enderror" value="{{ old('sender_area') ?? $booking->add_line_2 }}" >
                                        @error('sender_area')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ 'Pincode' }}<span class="text-danger">*</span></label>
                                        <select id="sender_pincode_id" name="sender_pincode_id" required class="form-control @error('sender_pincode_id') is-invalid @enderror">
                                            @if($booking->pincode_id)
                                                <option value="{{ $booking->pincode_id }}">{{ $booking->pincode->pincode }}</option>
                                            @endif
                                        </select>
                                        @error('sender_pincode_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{'City'}}</label>
                                        <input type="text" id="sender_city" name="sender_city" class="form-control @error('sender_city') is-invalid @enderror" value="{{ old('sender_city') ?? $booking->city }}">
                                        @error('sender_city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{'District'}}</label>
                                        <input type="text" id="sender_district" name="sender_district" class="form-control @error('sender_district') is-invalid @enderror" value="{{ old('sender_district') ?? $booking->district }}">
                                        @error('sender_district')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{'State'}}</label>
                                        <input type="text" id="sender_state" name="sender_state" class="form-control @error('sender_city') is-invalid @enderror" value="{{ old('sender_state') ?? $booking->state }}">
                                        @error('sender_state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{'Country'}}</label>
                                        <select id="sender_country" name="sender_country" class="form-control @error('sender_city') is-invalid @enderror">
                                            @foreach($senderCountryList as $country)
                                                {!! $country !!}
                                            @endforeach
                                        </select>
                                        @error('sender_country')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{'Mobile Number'}}</label>
                                        <input type="text" id="sender_mobile_number" name="sender_mobile_number" class="form-control @error('sender_mobile_number') is-invalid @enderror" value="{{ old('sender_mobile_number') ?? $booking->mobile_number }}">
                                        @error('sender_mobile_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{'Landline'}}</label>
                                        <input type="text" id="sender_phone_number" name="sender_phone_number" class="form-control @error('sender_phone_number') is-invalid @enderror" value="{{ old('sender_phone_number') ?? $booking->phone_number }}">
                                        @error('sender_phone_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{'Email'}}</label>
                                    <input type="email" id="sender_email" name="sender_email" class="form-control @error('sender_email') is-invalid @enderror" value="{{ old('sender_email') ?? $booking->email }}">
                                    @error('sender_email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="booking-card">
                                <div class="d-flex justify-content-between">
                                    <h4>{{ 'To Address' }}</h4>
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="receiver_sms" value="1" {{ $booking->sms_to_receiver == 1 ? 'checked' : '' }} class="form-check-input" id="receiver_sms" value="{{ old('receiver_sms') }}">
                                        <label class="form-check-label" for="receiver_sms">{{'SMS Notification'}}</label>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label>{{'Receiver Name'}}<span class="text-danger">*</span></label>
                                    <input type="text" name="receiver_name" required class="form-control @error('receiver_name') is-invalid @enderror" value="{{ old('receiver_name') ?? $delivery->receiver_name }}">
                                    @error('receiver_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>{{'Address'}}</label>
                                    <textarea type="textarea" id="receiver_address" name="receiver_address" class="form-control @error('receiver_address') is-invalid @enderror" >{{ old('receiver_address') ?? $delivery->add_line_1 }}</textarea>
                                    @error('receiver_address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{'Area'}}</label>
                                        <input type="text" id="receiver_area" name="receiver_area" class="form-control @error('receiver_area') is-invalid @enderror" value="{{ old('receiver_area') ?? $delivery->add_line_2 }}">
                                        @error('receiver_area')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ 'Pincode' }}<span class="text-danger">*</span></label>
                                        <select id="receiver_pincode_id" name="receiver_pincode_id" required class="form-control @error('receiver_pincode_id') is-invalid @enderror">
                                            @if($delivery->pincode_id)
                                                <option value="{{ $delivery->pincode_id }}">{{ $delivery->pincode->pincode }}</option>
                                            @endif
                                        </select>
                                        @error('receiver_pincode_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{'City'}}</label>
                                        <input type="text" id="receiver_city" name="receiver_city" class="form-control @error('receiver_city') is-invalid @enderror" value="{{ old('receiver_city') ?? $delivery->city }}">
                                        @error('receiver_city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{'District'}}</label>
                                        <input type="text" id="receiver_district" name="receiver_district" class="form-control @error('receiver_district') is-invalid @enderror" value="{{ old('receiver_district') ?? $delivery->district }}">
                                        @error('receiver_district')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{'State'}}</label>
                                        <input type="text" id="receiver_state" name="receiver_state" class="form-control @error('receiver_state') is-invalid @enderror" value="{{ old('receiver_state') ?? $delivery->state }}">
                                        @error('receiver_state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{'Country'}}</label>
                                        <select id="receiver_country" name="receiver_country" class="form-control @error('receiver_country') is-invalid @enderror">
                                            @foreach($receiverCountryList as $country)
                                                {!! $country !!}
                                            @endforeach
                                        </select>
                                        @error('receiver_country')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{'Mobile Number'}}</label>
                                        <input type="text" id="receiver_mobile_number" name="receiver_mobile_number" class="form-control @error('receiver_mobile_number') is-invalid @enderror" value="{{ old('receiver_mobile_number') ?? $delivery->mobile_number }}">
                                        @error('receiver_mobile_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{'Landline'}}</label>
                                        <input type="text" id="receiver_phone_number" name="receiver_phone_number" class="form-control @error('receiver_phone_number') is-invalid @enderror" value="{{ old('receiver_phone_number') ?? $delivery->phone_number }}">
                                        @error('receiver_phone_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{'Email'}}</label>
                                    <input type="text" id="receiver_email" name="receiver_email" class="form-control @error('receiver_email') is-invalid @enderror" value="{{ old('receiver_email') ?? $delivery->email }}">
                                    @error('receiver_email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pt-4 pb-4">
                <div class="col-10 mx-auto">
                    <div class="form-group ">
                        <div id="cap-dimensions" class="form-row vol-card mb-4" {{ $booking->consg_type == 'dox'? 'style=display:none;' : ''}}>
                            <section id="cap-length" class="form-group col-md-2" {{ $booking->consg_type == 'dox'? 'style=display:none;' : ''}}>
                                <label>{{ 'Capt. Length' }}</label>
                                <input id="length" type="text" readonly name="length" class="form-control" placeholder="Length (cm)" value="{{ $booking->length }}">
                            </section>
                            <section id="cap-breadth" class="form-group col-md-2" {{ $booking->consg_type == 'dox'? 'style=display:none;' : ''}}>
                                <label>{{ 'Capt. Breadth' }}</label>
                                <input id="breadth" type="text" readonly name="breadth" class="form-control" placeholder="Breadth (cm)" value="{{ $booking->breadth }}">
                            </section>
                            <section id="cap-height" class="form-group col-md-2" {{ $booking->consg_type == 'dox'? 'style=display:none;' : ''}}>
                                <label>{{ 'Capt. Height' }}</label>
                                <input id="height" type="text" readonly name="height" class="form-control" placeholder="Height (cm)" value="{{ $booking->height }}">
                            </section>

                        </div>
                        <div class="form-row vol-card mb-4">
                            <div id="act-length" class="form-group col-md-2" {{ $booking->consg_type == 'dox'? 'style=display:none;' : ''}}>
                                <label>{{ 'Act. Length(cm)' }}</label>
                                <input type="text" id="final_length" name="final_length" class="form-control" placeholder="Final Length (cm)" value="{{ old('final_length') }}">
                            </div>
                            <div id="act-breadth" class="form-group col-md-2" {{ $booking->consg_type == 'dox'? 'style=display:none;' : ''}}>
                                <label>{{ 'Act. Breadth(cm)' }}</label>
                                <input type="text" id="final_breadth" name="final_breadth" class="form-control" placeholder="Final Breadth (cm)" value="{{ old('final_breadth') }}">
                            </div>
                            <div id="act-heigtht" class="form-group col-md-2" {{ $booking->consg_type == 'dox'? 'style=display:none;' : ''}}>
                                <label>{{ 'Act. Height(cm)' }}</label>
                                <input type="text" id="final_height" name="final_height" class="form-control" placeholder="Final Height (cm)" value="{{ old('final_height') }}">
                            </div>
                            <div id="cap-weight" class="form-group col-md-2" {{ $booking->consg_type == 'non-dox'? 'style=display:none;' : ''}}>
                                <label>{{ 'Captured Weight' }}</label>
                                <input id="weight" type="text" readonly name="captured_weight" class="form-control" placeholder="Weight (kg)" value="{{ $booking->weight }}">
                            </div>
                            <div id="act-weight" class="form-group col-md-2" {{ $booking->consg_type == 'non-dox'? 'style=display:none;' : ''}}>
                                <label>{{ 'Act. Weight(kg)' }}</label>
                                <input type="text" id="final_weight" name="final_weight" class="form-control" placeholder="Final Weight (kg)" value="{{ old('final_weight') ?? $booking->final_weight }}">
                            </div>
                            <div id="vol-weight" class="form-group col-md-2" {{ $booking->consg_type == 'dox'? 'style=display:none;' : ''}}>
                                <label>{{ 'Volumetric Weight' }}</label>
                                <input id="vol_weight" type="text" name="vol_weight" class="form-control" readonly placeholder="Volumetric Weight (kg)" value="{{ old('vol_weight') ?? $booking->vol_weight }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="invisible">button</label>
                                <button id="weight-submit" form="calculateWeight" class="btn btn-primary">Calculate</button>
                            </div>
                        </div>
                        <div class="form-row vol-card mb-4">
                            <section class="form-group pl-2">
                                <label>{{'Booked Amount'}}</label>
                                <input id="bookedAmount" type="text" readonly name="booked_amount" class="form-control" value="{{ old('booked_amount') ?? $booking->booked_amount }}">
                            </section>
                            <section class="form-group pl-2">
                                <label>{{'Act. Amount'}}</label>
                                <input id="actualAmount" type="text" name="final_amount" class="form-control" value="{{ old('final_amount') ?? $booking->final_amount }}">
                            </section>
                            <section class="form-group pl-2">
                                <label>{{'Booked By'}}</label>
                                <input type="text" name="booking_user_name" class="form-control" list="booking_user" readonly value="{{ $user['username'].' ('.$user['first_name'].' '.$user['last_name'].')' }}">
                                <input type="hidden" name="booking_user_id" value="{{ $user['id'] }}">
                            </section>
                            <section class="form-group form-check p-4">
                                <input type="checkbox" id="risk_covered" name="insured" class="form-check-input" {{ $booking->insured == 1 ? 'checked' : '' }} value="1">
                                <label for="risk_covered" class="form-check-label">{{ 'Risk Covered' }}</label>
                            </section>
                            <section class="form-group">
                                <label class="invisible">{{'Declared Value'}}</label>
                                <input id="declared_consg_value" type="text" placeholder="Amount" name="declared_consg_value" class="form-control" readonly value="{{ old('declared_consg_value') ?? $booking->declared_consg_value }}">
                            </section>
                        </div>
                        <div class="form-row vol-card mb-4 hide_field">
                            <label>{{'Destination Branch'}}<span class="text-danger">*</span></label>
                            <select id="destination_branch_id" name="dest_branch_id" class="form-control @error('destination_branch_id') is-invalid @enderror">
                                @if($booking->dest_branch_id)
                                    <option value="{{ $booking->dest_branch_id }}">{{ $booking->branch->branch_name. ' ('.$booking->branch->code.')' }}</option>
                                @else
                                    <option value="" disabled selected>Select Destination Branch</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pb-2">
                <div class="col-8 mx-auto">
                    <div class="form-group">
                        <label>{{'Remarks'}}</label>
                        <textarea type="textarea" name="remarks" class="form-control" >{{ old('remarks') ?? $booking->remarks }}</textarea>
                    </div>
                </div>
            </div>
        </form>

        <div class="row mb-5 pb-2">
            <div class="col-8 mx-auto text-center">
                <div class="pt-lg-2">
                    <button form="editBooking" formmethod="POST" formaction="{{ route('bookings.update', $booking->id) }}" type="submit" class="btn btn-primary">{{ 'Update' }}</button>
                    <button for="deleteConfirm" class="btn btn-danger mx-sm-2" data-toggle="modal" {{ $deletePermission ? '': 'disabled' }} data-target="#deleteConfirm">{{ 'Delete' }}</button>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        {{ "Please confirm to delete this booking" }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                        <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" style="display:inline">
                            @method('DELETE')
                            {{ csrf_field() }}
                            <input type="submit" class="btn btn-danger btn-ok" value="{{ 'Confirm' }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .hide_field{
        display:none;
    }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        $('#consgType').on('change', function () {
            $("#subscription_id option").remove();
            let docType = $("#consgType").val();

            jQuery.ajax({
                url: "{{ url('/admin/subscription-list') }}",
                method: 'get',
                data: {
                    docType: docType,
                },

                success: function(result){
                    result.forEach(function (value) {
                        $("#subscription_id").append("<option value='"+value.id+"'>"+value.name+"</option>")
                    });
                }
            });
        });

        $('input[type=text]').val (function () {
            return this.value.toUpperCase();
        });
        $("#customer_id").on('change', function () {
            $("#customer_id option:not(:last)").remove();
        });
        $('#customer_id').select2({
            placeholder: "Choose Customer ID",
            allowClear: true,
            // tags: true,
            minimumInputLength: 3,
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

        $('#customer_id').on('change', function () {
            let custId = $("#customer_id").val();
            jQuery.ajax({
                url: "{{ url('/admin/customer-details') }}",
                method: 'get',
                data: {
                    id: custId,
                },
                success: function(result){
                    $("#sender_name").val(result.customer.customer_name);
                    $("#sender_city").val(result.customer.city);
                    $("#sender_state").val(result.customer.state);
                    $("#sender_pincode_id").append("<option value='"+result.customer.pincode_id+"'>"+result.pincode+"</option>");
                    $("#sender_mobile_number").val(result.customer.mobile_number);
                    $("#sender_email").val(result.customer.email);
                    $("#sender_phone_number").val(result.customer.phone_number);
                    $("#sender_address").val(result.customer.add_line_1);
                },
                error: function () {
                    $("#sender_name").val('');
                    $("#sender_city").val('');
                    $("#sender_state").val('');
                    $("#sender_pincode_id option").remove();
                    $("#sender_mobile_number").val('');
                    $("#sender_email").val('');
                    $("#sender_phone_number").val('');
                    $("#sender_address").val('');
                }
            });
        });


        $('#sender_pincode_id').select2({
            placeholder: "Choose Sender Pincode",
            // minimumInputLength: 2,
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
        $('#sender_pincode_id').on('change', function () {
            let pinId = $("#sender_pincode_id").val();
            jQuery.ajax({
                url: "{{ url('/admin/pincode-details') }}",
                method: 'get',
                data: {
                    id: pinId,
                },
                success: function(result){
                    $("#sender_city").val(result.city);
                    $("#sender_state").val(result.state);
                }});
        });

        $('#receiver_pincode_id').select2({
            placeholder: "Choose Receiver Pincode",
            // minimumInputLength: 2,
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
        $('#receiver_pincode_id').on('change', function () {
            let pinId = $("#receiver_pincode_id").val();
            $.ajax({
                url: "{{ url('/admin/pincode-details') }}",
                method: 'get',
                data: {
                    id: pinId,
                },
                success: function(result){
                    $("#receiver_city").val(result.city);
                    $("#receiver_state").val(result.state);
                }});
        });

        $("#receiver_pincode_id").on('change', function () {
            $("#receiver_pincode_id option:not(:last)").remove();
        });
        $('#receiver_pincode_id').on('change', function (e) {
            let pinId = $("#receiver_pincode_id").val();

            e.preventDefault();
            jQuery.ajax({
                url: "{{ url('/admin/serviceable-branches') }}",
                method: 'get',
                data: {
                    pinId: pinId,
                },
                success: function(result){
                    result.forEach(function (value) {
                        $("#destination_branch_id").append("<option value='"+value.id+"'>"+value.name+' ('+value.code+')'+"</option>")
                    });
                }
            });
        });

        // $(document).on('click', 'li', function(){
        //     $('#sender_pincode_id').val($(this).text());
        //     $('#senderPincodeList').fadeOut();
        // });

        $(document).ready(function(){
            let length = $("#act-length");
            let breadth = $("#act-breadth");
            let height = $("#act-heigtht");
            let weight = $("#act-weight");
            let volWeight = $("#vol-weight");
            let capDim = $("#cap-dimensions")
            let capLength = $("#cap-length");
            let capBreadth = $("#cap-breadth");
            let capHeight = $("#cap-height");
            let capWeight = $("#cap-weight");

            $('#consgType').on('change', function () {
                let optionVal = $(this).val();
                if (optionVal === 'non-dox') {
                    length.show();
                    length.prop('required', true);
                    breadth.show();
                    breadth.prop('required', true);
                    height.show();
                    height.prop('required', true);
                    weight.hide();
                    weight.val('');
                    volWeight.show();
                    capDim.show();
                    capLength.show();
                    capBreadth.show();
                    capHeight.show();
                    capWeight.hide();
                } else {
                    length.hide();
                    length.val('');
                    breadth.hide();
                    breadth.val('');
                    height.hide();
                    height.val('');
                    weight.show();
                    volWeight.val('');
                    volWeight.hide();
                    volWeight.hide();
                    capDim.hide();
                    capLength.hide();
                    capBreadth.hide();
                    capHeight.hide();
                    capWeight.show();
                }
            });

            $('#weight-submit').click(function(e){
                let lengthValue = $('#final_length').val();
                let breadthValue = $('#final_breadth').val();
                let heightValue = $('#final_height').val();
                let subsId = $("#subscription_id").val();
                let docType = $("#consgType").val();

                if (docType === 'dox') {
                    var weight = $("#final_weight").val();
                } else {
                    var weight = (lengthValue * breadthValue * heightValue) / 5000;
                    $('#vol_weight').val(weight);
                }

                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url: "{{ url('/admin/pricing-details/') }}",
                    method: 'get',
                    data: {
                        consgType: docType,
                        subId: subsId,
                        weight: weight
                    },
                    success: function(result){
                        $('#actualAmount').val(result.totalPrice);
                    }});
            });

            $('#risk_covered').on('click', function () {
                let declaredValue = $("#declared_consg_value");
                if ($(this).prop('checked')) {
                    declaredValue.prop('readonly', false);
                } else {
                    declaredValue.prop('readonly', true);
                    declaredValue.val('');
                }
            });

            $('#sender_sms').on('click', function () {
                if ($(this).is(":checked")) {
                    $("#sender_mobile_number").prop('required', true)
                } else {
                    $("#sender_mobile_number").prop('required', false)
                }
            });

            $('#receiver_sms').on('click', function () {
                if ($(this).is(":checked")) {
                    $("#receiver_mobile_number").prop('required', true)
                } else {
                    $("#receiver_mobile_number").prop('required', false)
                }
            });

        });


    </script>


@endsection
