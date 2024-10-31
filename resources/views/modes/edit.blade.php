@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>{{'Edit mode'}}</span></h1>
                </div>
{{--                <div>--}}
{{--                    <a class="btn btn-primary" href="{{ route('modes.index') }}">Back</a>--}}
{{--                </div>--}}
            </div>
        </div>

        <div class="row">
            <div class="col-8">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="">
                            <form id="editMode" action="{{ route('modes.update',$mode->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label>Code<span class="text-danger">*</span></label>
                                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') ?? $mode->code }}">
                                            @error('code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <label>Name<span class="text-danger">*</span></label>
                                            <input type="text" name="name" value="{{ old('name') ?? $mode->name }}" class="form-control @error('name') is-invalid @enderror">
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <label>type<span class="text-danger">*</span></label>
                                        <input type="text" name="type" value="{{ old('type') ?? $mode->type }}" class="form-control @error('type') is-invalid @enderror">
                                        @error('type')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <label>Description<span class="text-danger">*</span></label>
                                        <input type="text" name="description" value="{{ old('description') ?? $mode->description }}" class="form-control @error('description') is-invalid @enderror">
                                        @error('description')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                            <div class="pt-lg-2">
                                <button form="editMode" type="submit" class="btn btn-primary">{{ 'Update' }}</button>
                                <button class="btn btn-danger mx-sm-2" data-toggle="modal" data-target="#deleteConfirm">{{ 'Delete' }}</button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            {{ "Please confirm to delete this Modes" }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                                            <form action="{{ route('modes.destroy', $mode->id) }}" method="POST" style="display:inline">
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
