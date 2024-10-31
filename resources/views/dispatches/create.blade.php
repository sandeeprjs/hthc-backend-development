@extends('layouts.app')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Add Dispatches</span></h1>
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

                            <form action="{{ route('dispatches.store') }}" method="POST">
                                @csrf

                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <div class="form-group sender_div ">
                                        <label>Destination office <span class="text-danger">*</span></label>
                                        <select id="dest_id" name="dest_office_id"
                                                class="form-control @error('dest_office_id') is-invalid @enderror">
                                        @if(isset($branches))
                                            @foreach($branches as $branch)

                                                <!-- @if(isset($loggedOffice))
                                                    <option value="{{$branch->id}}" @if($branch->id == $loggedOffice->id) selected ="selected" @endif >
                                                                            {{$branch->code}}
                                                        </option>
                                                        @elseif(old('dest_id')) -->
                                                        <option value="{{$branch->code}}"
                                                                @if($branch->code == old('dest_office_id')) selected="selected" @endif >
                                                            {{$branch->code}}
                                                        </option>
                                                    <!-- @endif -->
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label>Consignment Number<span class="text-danger">*</span></label>
                                        <input type="text" name="consg_number"
                                               class="form-control @error('consg_number') is-invalid @enderror"
                                               required placeholder="Consignment Number"
                                               value="{{ old('consg_number') }}">
                                        @error('consg_number')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label>Vehicle Number</label>
                                        <input type="text" name="vehicle_number"
                                               class="form-control @error('vehicle_number') is-invalid @enderror"
                                                placeholder="Vehicle Number"
                                               value="{{ old('vehicle_number') }}">
                                        @error('vehicle_number')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label>Mode ID</label>
                                        <select input type="text" class="form-control" name="mode_id">
                                            @foreach($modes as $mode)
                                                <option value="{{$mode->id}}">{{$mode->name}}</option>
                                                @error('mode_id')
                                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                @enderror
                                            @endforeach
                                        </select>

                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <input type="text" name="status"
                                               class="form-control @error('status') is-invalid @enderror"
                                               placeholder="status" value="{{ old('status') }}">
                                        @error('status')
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
                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <style>
        .Incoming, .Outgoing {
            display: none;
        }

        #receiver_id_text, #dest_id_text {
            display: none;
        }

        .select2-results__message {
            display: none;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script text="javascript">

        $('#dest_id').select2({
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
