@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5 pl-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Add Pincode</span></h1>
{{--                    <div class="sb-page-header-subtitle">Please add the pin codes</div>--}}
                </div>
            </div>
        </div>

        <div class="row m-0">
            <div class="col-8">

                <form method="POST" action="{{ route('pincodes.store') }}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label>Pincode<span class="text-danger">*</span></label>
                                <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" required placeholder="Pincode" value="{{ old('pincode') }}">
                                @error('pincode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group required">
                                <label>City<span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" required placeholder="City" value="{{ old('city') }}">
                                @error('city')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>State</label>
                                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" placeholder="State" value="{{ old('state') }}">
                                @error('state')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col ">
                            <div class="form-group">
                                <label>Area</label>
                                <input type="text" name="area_name" class="form-control @error('area_name') is-invalid @enderror" placeholder="Area" value="{{ old('area_name') }}">
                                @error('area_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>District</label>
                                <input type="text" name="district" class="form-control @error('district') is-invalid @enderror" placeholder="District" value="{{ old('district') }}">
                                @error('district')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="country">{{'Country'}}</label>
                                <select id="country" class="form-control" name="country">
                                    @foreach($countryList as $country)
                                        {!! $country !!}
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" name="serviceable" checked value="1" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">{{'Serviceable'}}</label>
                    </div>
                    <button type="submit" class="btn btn-primary">{{'Submit'}}</button>
                </form>

            </div>
        </div>
    </div>
@endsection
