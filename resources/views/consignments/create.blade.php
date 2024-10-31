<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5 pl-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Generate Consignments</span></h1>
{{--                    <div class="sb-page-header-subtitle">Please add the pin codes</div>--}}
                </div>
            </div>
        </div>

        <div class="row m-0">
            <div class="col-8">

                <form method="POST" action="{{ route('consignments.print') }}" target="_blank">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label>Office Type<span class="text-danger">*</span></label>
                                <select id="office_type" required name="office_type" class="form-control @error('office_type') is-invalid @enderror">
                                    <option disabled selected>-- select --</option>
                                    <option value="HO">{{ 'Head Office' }}</option>
                                    <option value="BR">{{ 'Branch' }}</option>
                                    <option value="FR">{{ 'Partner' }}</option>
                                </select>
                                @error('office_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Office Id<span class="text-danger">*</span></label>
                                <select id="office_id" required name="office_id" class="form-control @error('office_id') is-invalid @enderror">
                                    <option disabled selected>-- select --</option>
                                </select>
                                @error('office_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Sheet Quantity<span class="text-danger">*</span></label>
                                <input type="text" required name="quantity" class="form-control col-md-1 @error('quantity') is-invalid @enderror" placeholder="Enter " value="{{ old('quantity') ?? '1' }}">
                                @error('quantity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">{{'Submit'}}</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        function officeType () {
            return $("#office_type").val();
        }

        $('#office_id').select2({
            placeholder: "Choose office ID",
            // minimumInputLength: 3,
            ajax: {
                url: "{{ url('admin/office-list') }}",
                dataType: 'json',
                data: function (params) {
                    return {
                        officeType: officeType(),
                        term: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                // cache: true
            }
        });
    </script>
@endsection
