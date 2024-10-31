@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Add Plan</span></h1>
                </div>
{{--                <div>--}}
{{--                    <a class="btn btn-primary" href="{{ route('subscriptions.index') }}">Back</a>--}}
{{--                </div>--}}
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="">

                            <form action="{{ route('subscriptions.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label>Name<span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required placeholder="Name" value="{{ old('name') }}">
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label>Consignment Type<span class="text-danger">*</span></label>
                                            <select input type="text" class="form-control @error('consg_type') is-invalid @enderror" name="consg_type">
                                                <option value="dox">Dox</option>
                                                <option value="non-dox">Non-Dox</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <label>Price<span class="text-danger">*</span></label>
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">Rs.</div>
                                            </div>
                                            <input type="text" required name="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror">
                                            @error('price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label>Delivery Time (Hrs)</label>
                                            <input class="form-control @error('max_delivery_time') is-invalid @enderror" name="max_delivery_time" placeholder="Delivery time (Hrs)" value="{{ old('max_delivery_time') }}">
                                            @error('max_delivery_time')
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
    </div>
@endsection
