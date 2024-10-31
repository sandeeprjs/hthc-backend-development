@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Add Reasons</span></h1>
                </div>
{{--                <div>--}}
{{--                    <a class="btn btn-primary" href="{{ route('pricing.index') }}"> Back</a>--}}
{{--                </div>--}}

            </div>

        </div>
        <div class="row">
            <div class="col-8">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <form action="{{ route('reasons.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Reason Type<span class="text-danger">*</span></label>
                                            <select input type="text" class="form-control" name="type">
                                                <option value=""> Select Reason</option>
                                                <option value="return">Return</option>
                                                <option value="cancel">Cancel</option>
                                            </select>
                                        </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label>Reason</label>
                                        <textarea class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Reasons">{{ old('name') }}</textarea>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 ">
                                    <button type="submit" class="btn btn-primary">{{ 'Submit' }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
