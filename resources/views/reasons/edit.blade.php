@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Edit Price</span></h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="">
                            <form id="editReason" action="{{ route('reasons.update',$reason->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label>Reason type<span class="text-danger">*</span></label>
                                            <select input type="text" class="form-control" name="type">
                                                <option value=""> Select Reason</option>
                                                <option {{ $reason->type == 'return'? 'selected' : ''}} value="return">Return</option>
                                                <option {{ $reason->type == 'cancel'? 'selected' : ''}} value="cancel">Cancel</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label>Reason</label>
                                            <textarea class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Remarks">{{ old('name') ?? $reason->name}}</textarea>
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="pt-lg-2">
                                <button form="editReason" type="submit" class="btn btn-primary">{{ 'Update' }}</button>
                                <button class="btn btn-danger mx-sm-2" data-toggle="modal" data-target="#deleteConfirm">{{ 'Delete' }}</button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            {{ "Please confirm to delete this Reason" }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                                            <form action="{{ route('reasons.destroy', $reason->id) }}" method="POST" style="display:inline">
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
