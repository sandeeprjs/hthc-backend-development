@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Edit Pincode</span></h1>
                </div>
{{--                <div>--}}
{{--                    <a class="btn btn-primary" href="{{ route('pincodes.index') }}">Back</a>--}}
{{--                </div>--}}
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <form id="editPincode">
                    @method('PUT')
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label>Pincode<span class="text-danger">*</span></label>
                                <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" placeholder="Pincode" required value="{{ old('pincode')? old('pincode'): $pincode->pincode }}">
                                @error('pincode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group required">
                                <label>City<span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" placeholder="City" required value="{{ old('city')? old('city'): $pincode->city }}">
                                @error('city')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" placeholder="State" value="{{ old('state')? old('state'): $pincode->state }}">
                                @error('state')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Area</label>
                                <input type="text" name="area_name" class="form-control @error('area_name') is-invalid @enderror" placeholder="Area" value="{{ old('area_name')? old('area_name'): $pincode->area_name }}">
                                @error('area_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>District</label>
                                <input type="text" name="district" class="form-control @error('district') is-invalid @enderror" placeholder="District" value="{{ old('district')? old('district'): $pincode->district }}">
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
                        <input type="checkbox" name="serviceable" {{ $pincode->serviceable == 1 ? 'checked' : '' }} value="1" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">{{'Serviceable'}}</label>
                    </div>
                </form>
            </div>
        </div>
        <div class="pt-lg-2">
            <button form="editPincode" formmethod="POST" formaction="{{ route('pincodes.update', $pincode->id) }}" type="submit" class="btn btn-primary">{{ 'Update' }}</button>
            <button for="deleteConfirm" class="btn btn-danger mx-sm-2" data-toggle="modal" data-target="#deleteConfirm">{{ 'Delete' }}</button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        {{ "Please confirm to delete this pincode" }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                        <form action="{{ route('pincodes.destroy', $pincode->id) }}" method="POST" style="display:inline">
                            @method('DELETE')
                            {{ csrf_field() }}
                            <input type="submit" class="btn btn-danger btn-ok" value="{{ 'Confirm' }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
