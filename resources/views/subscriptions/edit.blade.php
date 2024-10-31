@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>{{'Edit Plan'}}</span></h1>
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
                            <form id="editSubscription" action="{{ route('subscriptions.update',$subscription->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label>Name<span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') ?? $subscription->name }}">
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label>Consignment type<span class="text-danger">*</span></label>
                                            <select input type="text" class="form-control" name="consg_type">
                                                <option {{ $subscription->consg_type == 'dox'? 'selected' : ''}} value="dox">Dox</option>
                                                <option {{ $subscription->consg_type == 'non-dox'? 'selected' : ''}} value="non-dox">Non-Dox</option>
                                            </select>
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <label>Price<span class="text-danger">*</span></label>
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">Rs.</div>
                                            </div>
                                            <input type="text" name="price" value="{{ old('price') ?? $subscription->price }}" class="form-control @error('price') is-invalid @enderror">
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
                                            <input type="text" name="max_delivery_time" value="{{ old('max_delivery_time') ?? $subscription->max_delivery_time }}" class="form-control @error('max_delivery_time') is-invalid @enderror">
                                            @error('max_delivery_time')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="pt-lg-2">
                                <button form="editSubscription" type="submit" class="btn btn-primary">{{ 'Update' }}</button>
                                <button class="btn btn-danger mx-sm-2" data-toggle="modal" data-target="#deleteConfirm">{{ 'Delete' }}</button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            {{ "Please confirm to delete this subscription" }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                                            <form action="{{ route('subscriptions.destroy', $subscription->id) }}" method="POST" style="display:inline">
                                                @method('DELETE')
                                                {{ csrf_field() }}
                                                <input type="submit" class="btn btn-danger btn-ok" value="{{ 'Confirm' }}" />
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
@endsection
