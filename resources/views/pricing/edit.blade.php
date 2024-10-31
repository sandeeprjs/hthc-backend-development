@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Edit Price</span></h1>
                </div>
{{--                <div>--}}
{{--                    <a class="btn btn-primary" href="{{ route('pricing.index') }}"> Back</a>--}}
{{--                </div>--}}
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="">
                            <form id="editPricing" action="{{ route('pricing.update',$pricing->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>From Weight (KGs)<span class="text-danger">*</span></label>
                                            <input type="text" required name="from_weight_kgs" class="form-control @error('from_weight_kgs') is-invalid @enderror" placeholder="from weight kgs" value="{{ old('from_weight_kgs') ?? $pricing->from_weight_kgs }}">
                                            @error('from_weight_kgs')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>To Weight (KGs)<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('to_weight_kgs') is-invalid @enderror" required name="to_weight_kgs" placeholder="to weight kgs" value="{{ old('to_weight_kgs') ?? $pricing->to_weight_kgs}}">
                                            @error('to_weight_kgs')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <label>Price<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">Rs.</div>
                                            </div>
                                            <input class="form-control @error('price') is-invalid @enderror" required name="price" placeholder="Price" value="{{ old('price') ?? $pricing->price}}">
                                            @error('price')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Additional Weight (KGs)</label>
                                            <input class="form-control @error('addl_weight') is-invalid @enderror" name="addl_weight" placeholder="Add Weight" value="{{ old('addl_weight') ?? $pricing->addl_weight}}">
                                            @error('addl_weight')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <label>Additional Price (Rs.)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">Rs.</div>
                                            </div>
                                            <input class="form-control @error('addl_price') is-invalid @enderror" name="addl_price" placeholder="Add Price" value="{{ old('addl_price') ?? $pricing->addl_price}}">
                                            @error('addl_price')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Consignment type<span class="text-danger">*</span></label>
                                            <select input type="text" class="form-control" name="consg_type">
                                                <option {{ $pricing->consg_type == 'dox'? 'selected' : ''}} value="dox">Dox</option>
                                                <option {{ $pricing->consg_type == 'non-dox'? 'selected' : ''}} value="non-dox">Non-Dox</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <textarea class="form-control @error('remarks') is-invalid @enderror" name="remarks" placeholder="Remarks">{{ old('remarks') ?? $pricing->remarks}}</textarea>
                                            @error('remarks')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="pt-lg-2">
                                <button form="editPricing" type="submit" class="btn btn-primary">{{ 'Update' }}</button>
                                <button class="btn btn-danger mx-sm-2" data-toggle="modal" data-target="#deleteConfirm">{{ 'Delete' }}</button>
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
                                            <form action="{{ route('pricing.destroy', $pricing->id) }}" method="POST" style="display:inline">
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
