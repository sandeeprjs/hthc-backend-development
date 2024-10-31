@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>


@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Edit Customer</span></h1>

                </div>
                {{--                <div>--}}
                <a class="btn btn-primary" href="{{ route('customers.index') }}"> View Customers</a>
                {{--                </div>--}}

            </div>

        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="">


                            <form id="editCustomer" action="{{ route('customers.update',$customer->id) }}"
                                  method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Customer Code <span class="text-danger">*</span></label>
                                            <input type="text" name="code" value="{{ $customer->code }}"
                                                   class="form-control @error('code') is-invalid @enderror" required>
                                            @error('code')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Customer Name <span class="text-danger">*</span></label>
                                            <input type="text" name="customer_name"
                                                   value="{{ $customer->customer_name }}"
                                                   class=" form-control @error('customer_name') is-invalid @enderror"
                                                   required>
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
                                                      placeholder="Address 1"> {{ $customer->add_line_1 }} </textarea>
                                        </div>
                                    </div>


                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>City</label>
                                            <input type="text" name="city" value="{{ $customer->city }}"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>State</label>
                                            <input type="text" name="state" value="{{ $customer->state }}"
                                                   class="form-control">
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

                                            <label>Pincode <span class="text-danger">*</span></label>
                                            <select id="pincode_id" name="pincode_id"
                                                    class="form-control @error('pincode_id') is-invalid @enderror"
                                                    required>
                                                @if(isset($pincodes))
                                                    @foreach($pincodes as $pincode)
                                                        <option value="{{$pincode->id}}"
                                                                @if($pincode->id == $customer->pincode_id)selected="selected"@endif
                                                        >{{$pincode->pincode}}
                                                        </option>

                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Email ID</label>
                                            <input type="text" name="email" value="{{ $customer->email }}"
                                                   class="form-control  @error('email') is-invalid @enderror">
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
                                            <input type="text" name="add_line_2" value="{{ $customer->add_line_2 }}"
                                                   class="form-control  @error('add_line_2') is-invalid @enderror">
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
                                            <input type="text" name="district" value="{{ $customer->district }}"
                                                   class="form-control  @error('district') is-invalid @enderror">
                                            @error('district')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Mobile Number <span class="text-danger">*</span></label>
                                            <input class="form-control @error('mobile_number') is-invalid @enderror"
                                                   type="text" name="mobile_number"
                                                   value="{{ $customer->mobile_number }}" placeholder="Mobile Number"
                                                   maxlength="10" required>
                                            @error('mobile_number')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                            </form>
                            <div class="pt-lg-2">
                                <button form="editCustomer" formmethod="POST"
                                        formaction="{{ route('customers.update', $customer->id) }}" type="submit"
                                        class="btn btn-primary">{{ 'Update' }}</button>
                                <button for="deleteConfirm" class="btn btn-danger mx-sm-2" data-toggle="modal"
                                        data-target="#deleteConfirm">{{ 'Delete' }}</button>
                            </div>

                            <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog"
                                 aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            {{ "Please confirm to delete the record" }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ 'Close' }}</button>
                                            <form action="{{ route('customers.destroy',$customer->id) }}" method="POST"
                                                  style="display:inline">

                                                @csrf
                                                @method('DELETE')
                                                <input type="submit" class="btn btn-danger btn-ok"
                                                       value="{{ 'Confirm' }}"/>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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
