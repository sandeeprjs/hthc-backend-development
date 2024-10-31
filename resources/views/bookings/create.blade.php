<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>New Booking</span></h1>
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
        <form method="POST" action="{{ route('bookings.store') }}">
            {{ csrf_field() }}

            <div class="col-8 mx-auto form-row mb-4">
                <div class="form-group col-md-4">
                    <label>{{'Consignment Number'}}<span class="text-danger">*</span></label>
                    <input type="text" name="consg_number" required class="form-control @error('consg_number') is-invalid @enderror" value="{{ old('consg_number') }}">
                    @error('consg_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="consgType">{{ 'Consignment Type' }}<span class="text-danger">*</span></label>
                    <select id="consgType" class="form-control" name="consg_type">
                        <option value="" disabled selected class="form-control">Select a type</option>
                        <option value="dox" @if(old('consg_type') == 'dox' )) selected @endif >{{ 'Dox' }}</option>
                        <option value="non-dox" @if(old('consg_type') == 'non-dox')) selected @endif>{{ 'Non-Dox' }}</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="subscription_id">{{ 'Plans' }}<span class="text-danger">*</span></label>
                    <select id="subscription_id" class="form-control" required name="subscription_id">
                    <option value="" disabled selected class="form-control">Select a Plan</option>
                                    @if(isset($subscriptions))
                                        @foreach($subscriptions as $subscription)
                                            @if(old('subscription_id'))
                                                <option value="{{$subscription->id}}" @if($subscription->id == old('subscription_id')) selected ="selected" @endif >
                                                {{$subscription->name}}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif

{{--                        @foreach($subscriptionLists as $subscription)--}}
{{--                            {!! $subscription !!}--}}
{{--                        @endforeach--}}
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
                                <input type="checkbox" name="sender_sms" value="1" class="form-check-input @error('sender_sms') is-invalid @enderror" id="sender_sms" value="{{ old('sender_sms') }}">
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
                            <label for="customer_id">{{'Customer ID'}}<span class="text-danger">*</span></label>
{{--                            <input type="text" name="customer_val" id="customer_val" class="form-control input-lg" placeholder="Select Customer Id" />--}}
{{--                            <input type="hidden" id="customer_id" name="customer_id" value="">--}}
{{--                            <div id="customer_list"></div>--}}
                                <select id="customer_id" data-tags="true" required name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                                    @if(isset($customers))
                                        @foreach($customers as $customer)
                                            @if(old('customer_id'))
                                                <option value="{{$customer->id}}" @if($customer->id == old('customer_id')) selected ="selected" @endif >
                                                {{$customer->code}}
                                                </option>
                                            @endif
                                        @endforeach
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
                            <input type="text" id="sender_name" required name="sender_name" class="form-control @error('sender_name') is-invalid @enderror" value="{{ old('sender_name') }}">
                            @error('sender_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{'Address'}}</label>
                        <textarea type="textarea" id="sender_address" name="sender_address" class="form-control @error('sender_address') is-invalid @enderror" >{{ old('sender_address') }}</textarea>
                        @error('sender_address')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>{{'Area'}}</label>
                            <input type="text" id="sender_area" name="sender_area" class="form-control @error('sender_area') is-invalid @enderror" value="{{ old('sender_area') }}" >
                            @error('sender_area')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">

                            <label>{{ 'Pincode' }}<span class="text-danger">*</span></label>
                            <select id="sender_pincode_id" name="sender_pincode_id" required class="form-control @error('sender_pincode_id') is-invalid @enderror">
                                    @if(isset($pincodes))
                                        @foreach($pincodes as $pincode)
                                            @if(old('sender_pincode_id'))
                                                <option value="{{$pincode->id}}" @if($pincode->id == old('sender_pincode_id')) selected ="selected" @endif >
                                                {{$pincode->pincode}}
                                                </option>
                                            @endif
                                        @endforeach
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
                            <input type="text" id="sender_city" name="sender_city" class="form-control @error('sender_city') is-invalid @enderror" value="{{ old('sender_city') }}">
                            @error('sender_city')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>{{'District'}}</label>
                            <input type="text" id="sender_district" name="sender_district" class="form-control @error('sender_district') is-invalid @enderror" value="{{ old('sender_district') }}">
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
                            <input type="text" id="sender_state" name="sender_state" class="form-control @error('sender_city') is-invalid @enderror" value="{{ old('sender_state') }}">
                            @error('sender_state')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>{{'Country'}}</label>
                            <select id="sender_country" name="sender_country" class="form-control @error('sender_city') is-invalid @enderror">
                                @foreach($countryList as $country)
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
                            <input type="text" id="sender_mobile_number" name="sender_mobile_number" class="form-control @error('sender_mobile_number') is-invalid @enderror" value="{{ old('sender_mobile_number') }}">
                            @error('sender_mobile_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>{{'Landline'}}</label>
                            <input type="text" id="sender_phone_number" name="sender_phone_number" class="form-control @error('sender_phone_number') is-invalid @enderror" value="{{ old('sender_phone_number') }}">
                            @error('sender_phone_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{'Email'}}</label>
                        <input type="email" id="sender_email" name="sender_email" class="form-control @error('sender_email') is-invalid @enderror" value="{{ old('sender_email') }}">
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
                                <input type="checkbox" name="receiver_sms" value="1" class="form-check-input" id="receiver_sms" value="{{ old('receiver_sms') }}">
                                <label class="form-check-label" for="receiver_sms">{{'SMS Notification'}}</label>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>{{'Receiver Name'}}<span class="text-danger">*</span></label>
                            <input type="text" name="receiver_name" class="form-control @error('receiver_name') is-invalid @enderror" value="{{ old('receiver_name') }}" required>
                            @error('receiver_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>{{'Address'}}</label>
                            <textarea type="textarea" id="receiver_address" name="receiver_address" class="form-control @error('receiver_address') is-invalid @enderror" >{{ old('receiver_address') }}</textarea>
                            @error('receiver_address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>{{'Area'}}</label>
                                <input type="text" id="receiver_area" name="receiver_area" class="form-control @error('receiver_area') is-invalid @enderror" value="{{ old('receiver_area') }}">
                                @error('receiver_area')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{ 'Pincode' }}<span class="text-danger">*</span></label>

                                <select id="receiver_pincode_id" name="receiver_pincode_id" required class="form-control @error('receiver_pincode_id') is-invalid @enderror">
                                    @if(isset($pincodes))
                                        @foreach($pincodes as $pincode)
                                            @if(old('receiver_pincode_id'))
                                                        <option value="{{$pincode->id}}" @if($pincode->id == old('receiver_pincode_id')) selected ="selected" @endif >
                                                        {{$pincode->pincode}}
                                                        </option>
                                            @endif
                                        @endforeach
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
                                <input type="text" id="receiver_city" name="receiver_city" class="form-control @error('receiver_city') is-invalid @enderror" value="{{ old('receiver_city') }}">
                                @error('receiver_city')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{'District'}}</label>
                                <input type="text" id="receiver_district" name="receiver_district" class="form-control @error('receiver_district') is-invalid @enderror" value="{{ old('receiver_district') }}">
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
                                <input type="text" id="receiver_state" name="receiver_state" class="form-control @error('receiver_state') is-invalid @enderror" value="{{ old('receiver_state') }}">
                                @error('receiver_state')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{'Country'}}</label>
                                <select id="receiver_country" name="receiver_country" class="form-control @error('receiver_country') is-invalid @enderror">
                                    @foreach($countryList as $country)
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
                                <input type="text" id="receiver_mobile_number" name="receiver_mobile_number" class="form-control @error('receiver_mobile_number') is-invalid @enderror" value="{{ old('receiver_mobile_number') }}">
                                @error('receiver_mobile_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{'Landline'}}</label>
                                <input type="text" id="receiver_phone_number" name="receiver_phone_number" class="form-control @error('receiver_phone_number') is-invalid @enderror" value="{{ old('receiver_phone_number') }}">
                                @error('receiver_phone_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{'Email'}}</label>
                            <input type="text" id="receiver_email" name="receiver_email" class="form-control @error('receiver_email') is-invalid @enderror" value="{{ old('receiver_email') }}">
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
                    <div class="form-group">
                        <div class="form-row vol-card mb-4">
                            <section id="sec-length" class="form-group col-md-2" style="display: none;">
                                <label>{{ 'Capt. Length' }}<span class="text-danger">*</span></label>
                                <input id="length" type="text" name="length" class="form-control @error('length') is-invalid @enderror" placeholder="Length (cm)">
                                @error('length')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </section>
                            <section id="sec-breadth" class="form-group col-md-2" style="display: none;">
                                <label>{{ 'Capt. Breadth' }}<span class="text-danger">*</span></label>
                                <input id="breadth" type="text" name="breadth" class="form-control @error('breadth') is-invalid @enderror" placeholder="Breadth (cm)">
                                @error('breadth')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </section>
                            <section id="sec-height" class="form-group col-md-2" style="display: none;">
                                <label>{{ 'Capt. Height' }}<span class="text-danger">*</span></label>
                                <input id="height" type="text" name="height" class="form-control @error('height') is-invalid @enderror"  placeholder="Height (cm)">
                                @error('height')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </section>
                            <section id="sec-weight" class="form-group col-md-2">
                                <label>{{ 'Capt. Weight' }}<span class="text-danger">*</span></label>
                                <input id="weight" type="text" name="captured_weight" class="form-control @error('captured_weight') is-invalid @enderror" placeholder="Weight (kg)">
                                @error('captured_weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </section>
                            <section id="sec-vol-weight" class="form-group col-md-3" style="display: none;">
                                <label>{{ 'Volumetric Weight' }}</label>
                                <input id="vol_weight" type="text" name="vol_weight" class="form-control @error('vol_weight') is-invalid @enderror" readonly placeholder="Volumetric Weight (kg)">
                                @error('vol_weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </section>
                            <section class="form-group col-md-2">
                                <label class="invisible">Button</label>
                                <button id="weight-submit" form="calculateWeight" class="btn btn-primary">Calculate</button>
                            </section>
                        </div>
                        <div class="form-row vol-card mb-4">
                            <section class="form-group pl-2">
                                <label>{{'Booked Amount'}}<span class="text-danger">*</span></label>
                                <input id="bookedAmount" type="text" required name="booked_amount" class="form-control @error('booked_amount') is-invalid @enderror" >
                                @error('booked_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </section>
                            <section class="form-group pl-2">
                                <label>{{'Booked By'}}</label>
                                <input type="text" name="booking_user_name" class="form-control" list="booking_user" readonly value="{{ $user['username'].' ('.$user['first_name'].' '.$user['last_name'].')' }}">
                                <input type="hidden" name="booking_user_id" value="{{ $user['id'] }}">
                            </section>
                            <section class="form-group form-check p-4">
                                <label class="invisible">label</label>
                                <input type="checkbox" id="risk_covered" name="insured" class="form-check-input " value="1">
                                <label for="risk_covered" class="form-check-label ">{{ 'Risk Covered' }}</label>
                            </section>
                            <section class="form-group">

                                <label class="invisible">{{'Declared Value'}}</label>
                                <input id="declared_consg_value" type="text" placeholder="Amount" name="declared_consg_value" class="form-control @error('declared_consg_value') is-invalid @enderror" readonly>
                                @error('declared_consg_value')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </section>
                        </div>
                        <div class="form-row vol-card mb-4 hide_field">
                            <label>{{'Destination Branch'}}</label>
                            <select id="destination_branch_id" name="dest_branch_id" class="form-control @error('dest_branch_id') is-invalid @enderror">
                                <option value="" disabled selected>Select Destination Branch</option>
                            </select>
                            @error('dest_branch_id')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>


            <div class="row mb-5 pb-5">
                <div class="col-8 mx-auto">
                    <div class="form-group">
                        <label>{{'Remarks'}}</label>
                        <textarea type="textarea" name="remarks" class="form-control" >{{ old('remarks') }}</textarea>
                    </div>
                </div>
                <div class="col-8 mx-auto text-center">
                    <div class="w-50 mx-auto">
                        <button type="submit" class="mx-auto btn-lg btn btn-primary">Submit</button>
                    </div>

                </div>
            </div>
        </form>
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

        $("#customer_id").on('change', function () {
            $("#customer_id option:not(:last)").remove();
        });
        $('#customer_id').select2({
            placeholder: "Choose Customer ID",
            allowClear: true,
            tags: true,
            // minimumInputLength: 3,
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
            },
            createTag: function (tag) {

                // Check if the option is already there
                let found = false;
                $("#select2-customer_id-results li").each(function() {
                    if ($.trim(tag.term).toUpperCase() === $.trim($(this).text()).toUpperCase()) {
                        found = true;
                    }
                });

                // Show the suggestion only if a match was not found
                if (!found) {
                    return {
                        id: tag.term,
                        text: $.trim(tag.term).toUpperCase(),
                        isNew: true
                    };
                }
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
                    $('#customer_id option:selected').children('option:not(:first)').remove()
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
                }
            });
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
                }
            });
        });

        $('#receiver_pincode_id').on('change', function () {
            let pinId = $("#receiver_pincode_id").val();

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

        $(document).ready(function(){

            let length = $("#sec-length");
            let breadth = $("#sec-breadth");
            let height = $("#sec-height");
            let weight = $("#sec-weight");
            let volWeight = $("#sec-vol-weight");

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
                    weight.prop('required', false);
                    weight.val('');
                    volWeight.show();
                } else {
                    length.hide();
                    length.val('');
                    breadth.hide();
                    breadth.val('');
                    height.hide();
                    height.val('');
                    weight.show();
                    weight.prop('required', true);
                    volWeight.hide();
                }
            });

            $('#weight-submit').click(function(e){
                let lengthValue = $('#length').val();
                let breadthValue = $('#breadth').val();
                let heightValue = $('#height').val();
                let subsId = $("#subscription_id" ).val();
                let docType = $("#consgType").val();

                if (docType === 'dox') {
                    var weight = $("#weight").val();
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
                        $('#bookedAmount').val(result.totalPrice);
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
