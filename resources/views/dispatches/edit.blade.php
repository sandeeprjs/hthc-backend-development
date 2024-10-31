@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>{{'Edit dispatch'}}</span></h1>
                </div>
{{--                <div>--}}
{{--                    <a class="btn btn-primary" href="{{ route('dispatches.index') }}">Back</a>--}}
{{--                </div>--}}
            </div>
        </div>

        <div class="row">
            <div class="col-8">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="">
                            <form id="editDispatch" action="{{ route('dispatches.update',$dispatch->id) }}" method="POST">
                                @csrf
                                @method('PUT')


                                        <div class="col-xs-12 col-sm-12 col-md-4">
                                            <div class="form-group sender_div required">
                                                <label>Destination <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="dest_office_id"
                                                       placeholder="Destination" id="sender_id_text" >
                                                <select id="sender_id" name="dest_office_id" class="form-control @error('dest_office_id') is-invalid @enderror">
                                                    @if(isset($branches))
                                                        @foreach($branches as $branch)

                                                            @if(isset($loggedOffice))
                                                                <option value="{{$branch->code}}" @if($branch->code == $dispatch->dest_office_id) selected ="selected" @endif >
                                                                    {{$branch->code}}
                                                                </option>
                                                            @elseif(old('sender_id'))
                                                                <option value="{{$branch->code}}" @if($branch->code == old('dest_office_id')) selected ="selected" @endif >
                                                                    {{$branch->code}}
                                                                </option>
                                                            @endif
                                                        @endforeach

                                                    @endif

                                                </select>
                                            </div>
                                        </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <label>Consignment Number<span class="text-danger">*</span></label>
                                            <input type="text" name="consg_number" value="{{ old('consg_number') ?? $dispatch->consg_number }}" class="form-control @error('consg_number') is-invalid @enderror">
                                            @error('consg_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <label>vehicle No<span class="text-danger">*</span></label>
                                        <input type="text" name="vehicle_number" value="{{ old('vehicle_number') ?? $dispatch->vehicle_number }}" class="form-control @error('vehicle_number') is-invalid @enderror">
                                        @error('vehicle_number')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <label>Status<span class="text-danger">*</span></label>
                                        <input type="text" name="status" value="{{ old('status') ?? $dispatch->status }}" class="form-control @error('status') is-invalid @enderror">
                                        @error('status')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                            </form>
                            <div class="pt-lg-2">
                                <button form="editDispatch" type="submit" class="btn btn-primary">{{ 'Update' }}</button>
                                <button class="btn btn-danger mx-sm-2" data-toggle="modal" data-target="#deleteConfirm">{{ 'Delete' }}</button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            {{ "Please confirm to delete this dispatches" }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'Close' }}</button>
                                            <form action="{{ route('dispatches.destroy', $dispatch->id) }}" method="POST" style="display:inline">
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
    <style>
        .Incoming,.Outgoing {
            display:none;
        }
        #receiver_id_text, #sender_id_text{
            display:none;
        }
        .select2-results__message{
            display:none;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script text="javascript">

        $('#sender_id').select2({
            placeholder: "Choose Sender",
            minimumInputLength: 2,
            ajax: {
                url: "{{ url('/admin/branch-franchisee') }}",
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
