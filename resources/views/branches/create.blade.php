
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@extends('layouts.app')

@section('content')
<div class="container">

    <div class="sb-page-header-content py-5">
        <div class="d-flex justify-content-between">
            <div>
                <h1 class="sb-page-header-title"><span>@if(isset($branch)) Edit  @else Add @endif Branch</span></h1>
            </div>
                <a class="btn btn-primary" href="{{ route('branches.index') }}"> View Branches</a>
        </div>
    </div>
        @if ($message = Session::get('failed'))
                                    <div class="successMessage alert alert-danger">
                                        <p>{{ $message }}</p>
                                    </div>
        @endif
<div class="row">
    <div class="col-md-8">

    <div class="row ">
        <div class="col-md-12">
            <div class="">
                <div class="">

                    <form id="branch_form" method="POST" action="@if(isset($branch)){{ route('branches.update',$branch->id)}} @else {{ route('branches.store') }} @endif ">
                        @if(isset($branch))
                            @method('PUT')
                        @endif
                        @csrf

                       </div>
                      
                <div class="row">
                        <div class="col-md-4">
                        <div class="form-group">
                        <label for="branch_type">{{ __('Branch Type') }} <span class="text-danger">*</span></label>

                        <select  name="branch_type" id="branch_type"  required class="form-control @error('branch_type') is-invalid @enderror">
                            <option value="">Select Type</option>
                            <option value="HO" @if(isset($branch)){{ ($branch->branch_type) == 'HO' ? 'selected':'' }}
                                @elseif (old('branch_type') == 'HO' ) selected
                                @endif
                             >Head Office</option>
                             <option value="BR"  @if(isset($branch)){{ ($branch->branch_type) == 'BR' ? 'selected':'' }}
                                @elseif (old('branch_type') == 'BR' ) selected
                                @endif
                             >Branch Office</option>
                          
                            </select>
                            @error('branch_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        </div>
                <div class="col-md-4">
                        <div class="form-group">
                            <label for="code" >{{ __('Branch Code') }} <span class="text-danger">*</span></label>


                                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" required value="@if(isset($branch)){{$branch->code}}@else{{ old('code') }}@endif" >

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                       
                    <div class="col-md-4">
                        <div class="form-group ">
                            <label for="branch_name" >{{ __(' Branch Name') }} <span class="text-danger">*</span> </label>


                                <input id="branch_name" type="text" class="form-control @error('branch_name') is-invalid @enderror" name="branch_name" required value="@if(isset($branch)){{$branch->branch_name}}@else{{ old('branch_name') }}@endif" >

                                @error('branch_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="incharge_name" >{{ __('Contact Person Name') }} </label>

                                <input id="incharge_name" type="text" class="text_field form-control @error('incharge_name') is-invalid @enderror" name="incharge_name" value="@if(isset($branch)){{$branch->incharge_name}}@else{{ old('incharge_name') }}@endif" >

                                @error('incharge_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>





                    <div class="col-md-4">
                        <div class="form-group origin-pin">
                            <label for="pincode_id" >{{ __(' Origin Pincode ') }} <span class="text-danger">*</span></label>


                                <select id="pincode_id" name="pincode_id" class="form-control @error('pincode_id') is-invalid @enderror" required>
                                                            @if(isset($pincodes))
                                                             
                                                            @foreach($pincodes as $pincode)
                                                             
                                                                @if(isset($branch))
                                                                            <option value="{{$pincode->id}}" @if($pincode->id == $branch->pincode_id) selected ="selected" @endif >
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
                        <div class="form-group">
                            <label for="service_pincode_id">{{ __(' Serviceable Pincodes ') }} <span class="text-danger">*</span></label>
                                <select id="service_pincode_id" name="service_pincode_id[]" required class="form-control @error('service_pincode_id') is-invalid @enderror" multiple style="height:auto">

                                    @if(isset($pincodes))
                                            @foreach($pincodes as $pincode)
                                            <option value="{{$pincode->id}}"

                                            @if(isset($branch))
                                                    @foreach($branch->serviceablePins as $serviceablePin)
                                                        @if($serviceablePin->pincode_id == $pincode->id)selected="selected"

                                                        @endif
                                                    @endforeach
                                            @elseif(old('service_pincode_id'))  
                                                  @foreach(old('service_pincode_id') as $key => $serviceablePin)
                                                        @if($serviceablePin == $pincode->id)selected="selected"

                                                        @endif
                                                    @endforeach
                                            @endif

                                            >{{$pincode->pincode}}</option>

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





                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="add_line_1" >{{ __(' Address') }}</label>
                                <input id="add_line_1" type="text" class="form-control @error('add_line_1') is-invalid @enderror" name="add_line_1" value="@if(isset($branch)){{$branch->add_line_1}}@else{{ old('add_line_1') }}@endif" >

                                @error('add_line_1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="city">{{ __(' City ') }} </label>
                                <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="@if(isset($branch)){{$branch->city}}@else{{ old('city') }}@endif" >

                                @error('city')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="state">{{ __(' State ') }}</label>
                                <input id="state" type="text" class="form-control @error('state') is-invalid @enderror" name="state" value="@if(isset($branch)){{$branch->state}}@else{{ old('state') }}@endif" >

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
                        <div class="form-group">
                            <label for="mobile_number">{{ __('Mobile Number') }} <span class="text-danger">*</span></label>

                                <input id="mobile_number" type="text" class="form-control @error('mobile_number') is-invalid @enderror" name="mobile_number" required value="@if(isset($branch)){{$branch->mobile_number}}@else{{ old('mobile_number') }}@endif"  maxlength="10">

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

                                <input id="phone_number" type="text" maxlength="14" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="@if(isset($branch)){{$branch->phone_number}}@else{{ old('phone_number') }}@endif" >

                                @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email" >{{ __('Email') }}</label>

                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="@if(isset($branch)){{$branch->email}}@else{{ old('email') }}@endif" autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                  
                    </form>
                   
                    <div class="pt-lg-2 col-md-12">
                              
                                <button form="branch_form" formmethod="POST" type="submit" class="btn btn-primary">
                                                    @if(isset($branch)){{ __('Update') }} @else {{ __('Submit') }} @endif
                                </button>
                                @if(isset($branch))
                                    <button class="btn btn-danger"  data-toggle="modal" data-target="#deleteConfirm">{{ 'Delete' }}</button>
                                @endif 
                              
                    </div>
                            @if(isset($branch))
                            <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    {{ "Please confirm to delete the record" }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                                                    <form action="{{ route('branches.destroy',$branch->id) }}" method="POST" style="display:inline">

                                                        @csrf
                                                        @method('DELETE')
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
</div>
</div>
   

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>



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
        placeholder: "Choose Serviceable Pincodes...",
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
    </script>
@endsection





