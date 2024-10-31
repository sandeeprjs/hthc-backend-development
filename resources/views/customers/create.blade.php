@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Add Customer</span></h1>

                </div>

                <a class="btn btn-primary" href="{{ route('customers.index') }}"> View Customers</a>


            </div>

        </div>

        <div class="row">
            <div class="col-md-8">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="">


                            <form action="{{ route('customers.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Customer Code <span class="text-danger">*</span></label>
                                            <input type="text" name="code"
                                                   class="form-control @error('code') is-invalid @enderror"
                                                   value="{{ old('code') }}"
                                                   placeholder="Code" required>
                                            @error('code')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group required">
                                            <label>Customer Name <span class="text-danger">*</span></label>
                                            <input type="text" name="customer_name"
                                                   class="form-control  @error('customer_name') is-invalid @enderror"
                                                   value="{{ old('customer_name') }}"
                                                   placeholder="Customer Name" required>
                                            @error('customer_name')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea class="form-control" name="add_line_1"
                                                      placeholder="Address 1"> {{ old('add_line_1')}} </textarea>
                                        </div>
                                    </div>


                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>City</label>
                                            <input class="form-control text_field" name="city"
                                                   placeholder="city" value="{{ old('city')}}">
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>State</label>
                                            <input class="form-control text_field" name="state"
                                                   placeholder="State" value="{{ old('state')}}">
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select id="country_id" class="form-control" name="country_id">
                                                @foreach($countryList as $country)
                                                    {!! $country !!}
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">

                                            <label>Pincode<span class="text-danger">*</span></label>
                                            <select id="pincode_id" name="pincode_id"
                                                    class="form-control @error('pincode_id') is-invalid @enderror"
                                                    required>
                                                @if(isset($pincodes))
                                                    @foreach($pincodes as $pincode)
                                                        @if(old('pincode_id'))
                                                            <option value="{{$pincode->id}}"
                                                                    @if($pincode->id == old('pincode_id'))selected="selected"@endif
                                                            >{{$pincode->pincode}}
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

                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input class="form-control  @error('email') is-invalid @enderror"
                                                   type="email" name="email"
                                                   placeholder="Email" value="{{ old('email')}}">
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Area</label>
                                            <input class="form-control  @error('add_line_2') is-invalid @enderror"
                                                   type="text" name="add_line_2"
                                                   placeholder="area" value="{{ old('add_line_2')}}">
                                            @error('add_line_2')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>District</label>
                                            <input class="form-control  @error('district') is-invalid @enderror"
                                                   type="text" name="district"
                                                   placeholder="district" value="{{ old('district')}}">
                                            @error('district')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Mobile Number<span class="text-danger">*</span></label>
                                            <input class="form-control @error('mobile_number') is-invalid @enderror"
                                                   type="text" name="mobile_number"
                                                   value="{{ old('mobile_number') }}" placeholder="Mobile Number"
                                                   maxlength="10" required>
                                            @error('mobile_number')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-xs-12 col-sm-12 col-md-12 ">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>

                            </form>

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
