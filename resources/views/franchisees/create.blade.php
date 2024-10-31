@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css" rel="stylesheet" />

@section('content')
<div class="container">

    <div class="sb-page-header-content py-5">
        <div class="d-flex justify-content-between">
            <div>
                <h1 class="sb-page-header-title"><span>@if(isset($franchisee)) Edit @else Add @endif Partner</span></h1>
{{--                <div class="sb-page-header-subtitle">Create new Franchisee in HTHC</div>--}}
            </div>
            <div>
                <a class="btn btn-primary" href="{{ route('franchisees.index') }}"> View Partners</a>
            </div>

        </div>

    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="">
                <div class="">
                    <form id="franchisee_form" method="POST"  enctype="multipart/form-data" action="@if(isset($franchisee)){{ route('franchisees.update',$franchisee->id)}} @else {{ route('franchisees.store') }} @endif ">
                        @if(isset($franchisee))
                            @method('PUT')
                        @endif

                       @csrf

                       <div class="row">
                       <div class="col-md-12">
                        <div class="form-group">
                                <label for="partner_type">{{ __(' Partner Type') }} <span class="text-danger">*</span></label>
                                <input id="partner_type" type="radio" class=" @error('franchisee_type') is-invalid @enderror" name="franchisee_type" value="BOOKING" @if(isset($franchisee)) {{ $franchisee->franchisee_type == 'BOOKING' ? 'checked' : '' }} @endif />
                                Booking 
                                <input id="partner_type" type="radio" class=" @error('franchisee_type') is-invalid @enderror" name="franchisee_type" value="DELIVERY" @if(isset($franchisee)) {{ $franchisee->franchisee_type == 'DELIVERY' ? 'checked' : '' }} @endif/>
                                Delivery
                                <input id="partner_type" type="radio" class=" @error('franchisee_type') is-invalid @enderror" name="franchisee_type" value="BOTH" @if(isset($franchisee)) {{ $franchisee->franchisee_type == 'BOTH' ? 'checked' : '' }} @else  {{ 'checked' }} @endif/>
                                Both 
                                @error('add_line_1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-4">
                        <div class="form-group">
                        <label for="branch_id">{{ __('Branch') }} <span class="text-danger">*</span></label>
                        <select class="form-control @error('branch_id') is-invalid @enderror" name="branch_id" id="branch_id" required>
                            <option value=''>Select Branch</option>
                            @foreach ($branches as $key => $value)
                                <option value="{{ $value->id }}"
                                @if(isset($franchisee)){{ ($franchisee->branch_id) == $value->id ? 'selected':'' }}
                                @elseif (!$user->isAdmin()) {{($user->office_id) == $value->id ? 'selected':''}}
                                @elseif (old('branch_id') == $value->id ) selected
                                @endif >
                                    {{ $value->code }}  [ {{ $value->branch_name }} ] 
                                </option>
                            @endforeach
                            </select>
                            @error('branch_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="code" >{{ __('Partner Code') }} <span class="text-danger">*</span></label>
                            <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" required name="code" value="@if(isset($franchisee)){{$franchisee->code}}@else{{ old('code') }}@endif" >
                            @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="enterprise_name">{{ __('Partner Name') }} <span class="text-danger">*</span></label>
                            <input id="enterprise_name" type="text" class="form-control @error('enterprise_name') is-invalid @enderror" required name="enterprise_name" value="@if(isset($franchisee)){{$franchisee->enterprise_name}}@else{{ old('enterprise_name') }}@endif" >
                                @error('enterprise_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="contact_person_name">{{ __(' Contact Person ') }}</label>
                            <input id="contact_person_name" type="text" class="form-control @error('contact_person_name') is-invalid @enderror" name="contact_person_name" value="@if(isset($franchisee)){{$franchisee->contact_person_name}}@else{{ old('contact_person_name') }}@endif" >
                            @error('contact_person_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                        <div class="form-group required">
                            <label for="pincode_id" >{{ __(' Origin Pincode ') }} <span class="text-danger">*</span></label>
                            <select id="pincode_id" name="pincode_id" class="form-control @error('pincode_id') is-invalid @enderror" required>
                                @if(isset($pincodes))
                                @foreach($pincodes as $pincode)
                                @if(isset($franchisee))
                                            <option value="{{$pincode->id}}" @if($pincode->id == $franchisee->pincode_id) selected ="selected" @endif >
                                            {{$pincode->pincode}}
                                            </option>
                                @elseif(old('pincode_id'))
                                            <option value="{{$pincode->id}}" @if($pincode->id == old('pincode_id')) selected ="selected" @endif >
                                            {{$pincode->pincode}}
                                            </option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                                @error('pincode_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                        <div class="form-group required">
                            <label for="service_pincode_id">{{ __(' Serviceable Pincodes ') }} <span class="text-danger">*</span></label>
                            <select id="service_pincode_id" name="service_pincode_id[]" required class="form-control @error('service_pincode_id') is-invalid @enderror" multiple
                                 style="height:auto !important;">
                                    @if(isset($pincodes))
                                    @foreach($pincodes as $pincode)
                                    <option value="{{$pincode->id}}"
                                    @if(isset($franchisee))
                                            @foreach($franchisee->serviceablePins as $serviceablePin)
                                                @if($serviceablePin->pincode_id == $pincode->id)selected="selected"
                                                @endif
                                            @endforeach
                                    @elseif(old('service_pincode_id'))  
                                            @foreach(old('service_pincode_id') as $key => $serviceablePin)
                                                @if($serviceablePin == $pincode->id)selected="selected"
                                                @endif
                                            @endforeach
                                    @endif >
                                    {{$pincode->pincode}}
                                    </option>
                                    @endforeach
                                    @endif

                                </select>
                                @error('service_pincode_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                                <div class="form-group">
                                    <label for="current_bank_name" class="">{{ __('Current Bank Name') }} </label>

                                    <input id="current_bank_name" type="text"
                                           class="form-control @error('current_bank_name') is-invalid @enderror"
                                           name="current_bank_name"
                                           value="@if(isset($franchisee)){{$franchisee->current_bank_name}}@else{{ old('current_bank_name') }}@endif"
                                           
                                    >

                                    @error('current_bank_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="branch_name" class="">{{ __('Branch name') }}</label>

                                    <input id="branch_name" type="text"
                                           class="form-control @error('branch_name') is-invalid @enderror"
                                           name="branch_name"
                                           value="@if(isset($franchisee)){{$franchisee->branch_name}}@else{{ old('branch_name') }}@endif"
                                           
                                    >

                                    @error('branch_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="account_number" class="">{{ __('Account Number') }} </label>

                                    <input id="account_number" type="text"
                                           class="form-control @error('account_number') is-invalid @enderror"
                                           name="account_number"
                                           value="@if(isset($franchisee)){{$franchisee->account_number}}@else{{ old('account_number') }}@endif"
                                           
                                    >

                                    @error('account_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="ifsc_code" class="">{{ __('IFSC Code') }} </label>

                                    <input id="ifsc_code" type="text"
                                           class="form-control @error('ifsc_code') is-invalid @enderror"
                                           name="ifsc_code"
                                           value="@if(isset($franchisee)){{$franchisee->ifsc_code}}@else{{ old('ifsc_code') }}@endif"
                                           
                                    >

                                    @error('ifsc_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="avatar" class="">{{ __('Profile Pic') }}</label>

                                    <input id="avatar" type="file"
                                           class="form-control @error('avatar') is-invalid @enderror"
                                           name="avatar"
                                           value="@if(isset($franchisee)){{$franchisee->avatar}}@else{{ old('avatar') }}@endif"
                                           
                                    >


                                    @error('avatar')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                @if(isset($franchisee->avatar))
                                <img src="{{ asset('storage/uploads/partners/photo/'.$franchisee->avatar) }}" alt=""  width="100" />
                                @endif
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="doc_proof" class="">{{ __('PAN / Aadhar / Voter ID proof') }} </label>

                                    <input id="doc_proof" type="file"
                                           class="form-control @error('doc_proof') is-invalid @enderror"
                                           name="doc_proof"
                                           value="@if(isset($franchisee)){{$franchisee->doc_proof}}@else{{ old('doc_proof') }}@endif"
                                          
                                    >

                                    @error('doc_proof')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                   
                                </div>
                                @if(isset($franchisee->avatar))
                                    <img src="{{ asset('storage/uploads/partners/idproof/'.$franchisee->doc_proof) }}" alt=""  width="100" />
                                    @endif
                            </div>
                        

                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="add_line_1">{{ __(' Address') }}</label>
                            <input id="add_line_1" type="text" class="form-control @error('add_line_1') is-invalid @enderror" name="add_line_1" value="@if(isset($franchisee)){{$franchisee->add_line_1}}@else{{ old('add_line_1') }}@endif" >
                            @error('add_line_1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="city" class="col-md-4 col-form-label text-md-right">{{ __(' City ') }}</label>
                            <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="@if(isset($franchisee)){{$franchisee->city}}@else{{ old('city') }}@endif" >
                            @error('city')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="state" class="col-md-4 col-form-label text-md-right">{{ __(' State ') }}</label>
                            <input id="state" type="text" class="form-control @error('state') is-invalid @enderror" name="state" value="@if(isset($franchisee)){{$franchisee->state}}@else{{ old('state') }}@endif" >
                            @error('state')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                        <div class="form-group">
                                <label for="country_id">{{ __(' Country ') }} </label>
                                <select id="country_id" name="country_id" class="form-control @error('service_pincode_id') is-invalid @enderror">
                                    @if(isset($countries))
                                            @foreach($countries as $country)
                                            <option value="{{$country->id}}" @if($country->name=='India') selected @endif >{{$country->name}}</option>
                                            @endforeach
                                    @endif
                                    </select>
                                    @error('country_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                        </div>
                        
                        <div class="col-md-4">
                        <div class="form-group required">
                            <label for="mobile_number">{{ __('Mobile Number') }} <span class="text-danger">*</span></label>
                            <input id="mobile_number" type="text" class="form-control @error('mobile_number') is-invalid @enderror" required name="mobile_number" value="@if(isset($franchisee)){{$franchisee->mobile_number}}@else{{ old('mobile_number') }}@endif" maxlength="10">
                            @error('mobile_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="phone_number">{{ __('Landline Number') }}</label>
                            <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="@if(isset($franchisee)){{$franchisee->phone_number}}@else{{ old('phone_number') }}@endif" maxlength="14" >
                            @error('phone_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>
                        </div>
                       

                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="@if(isset($franchisee)){{$franchisee->email}}@else{{ old('email') }}@endif" autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>
                        </div>
                        </div>
                        @if(isset($franchisee))
                        <input type="hidden" name='old_avatar' value="{{ $franchisee->avatar }}"/>
                        <input type="hidden" name='old_doc_proof' value="{{$franchisee->doc_proof}}"/>
                    @endif
                               
                    </form>
                    <div class="pt-lg-2 col-md-12">
                    
                        <button form="franchisee_form" formmethod="POST" type="submit" class="btn btn-primary">
                                            @if(isset($franchisee)){{ __('Update') }} @else {{ __('Submit') }} @endif
                        </button>
                        @if(isset($franchisee))
                        <button class="btn btn-danger"  data-toggle="modal" data-target="#deleteConfirmFR">{{ 'Delete' }}</button>
                        @endif
                    </div>
                            @if(isset($franchisee))
                            
                            <div class="modal fade" id="deleteConfirmFR" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                     {{ "Please confirm to delete the record" }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                                                    <form action="{{ route('franchisees.destroy', $franchisee->id) }}" method="POST" style="display:inline">
                                                          @method('DELETE')
                                                          {{ csrf_field() }}
                                                        <input type="submit" class="btn btn-danger btn-ok" value="{{ 'Confirm' }}" />
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                            
                               
                            @endif

                </div>
            </div>
        </div>
    </div>
</div>




    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.js"></script>

    <script type="text/javascript">

         
        $('#pincode_id').select2({
            placeholder: "Choose Origin Pincode",
            //minimumInputLength: 2,
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

        $('#service_pincode_id').select2({
        placeholder: "Choose Pincode...",
      //  minimumInputLength: 2,
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
    $(document).ready(function() {

        $("a#single_image").fancybox();

         $('.image-link').magnificPopup({type:'image'});
         $( ".photopopup" ).on({
        popupbeforeposition: function() {
            var maxHeight = $( window ).height() - 60 + "px";
            $( ".photopopup img" ).css( "max-height", maxHeight );
        }
    });
    });
    </script>
  @endsection
