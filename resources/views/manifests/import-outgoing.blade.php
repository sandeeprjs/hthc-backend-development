<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Bulk Outgoing Manifest Import</span></h1>
                </div>
                <a class="btn btn-primary" href="{{ route('manifests.outgoing.create') }}"> Normal Outgoing Manifests</a>
            </div>
        </div>
        <form method="POST" action="{{ route('manifests.import.outgoing.create') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-8 mt-4" >
                    <div class="booking-card pb-5">
                        <div class="form-group">
                            <h5>{{ 'Select Receiver' }}<span class="text-danger">*</span></h5>
                            <section class="input-group pl-2" id="partnerSelect">
                                <div class="input-group-prepend">
                                    <select class="input-group-text" id="office_type" name="office_type">
                                        <option selected disabled>Office Type</option>
                                        <option value="HO">Head Office</option>
                                        <option value="BR">Branch</option>
                                        <option value="FR">Partner</option>
                                    </select>
                                </div>
                                <select id="receiver_id" required name="receiver_id" class="form-control @error('receiver_id') is-invalid @enderror">
                                    @if( request()->input('receiver_id') )
                                        <option value="{{ request()->input('receiver_id') }}">{{ $franchisee->code }}</option>
                                    @endif
                                </select>
                                @error('receiver_id')
                                <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                @enderror
                            </section>
                        </div>

                        <div class="d-flex justify-content-between">
                            <h5>{{ 'Upload File' }}</h5>
                            <div class="form-group form-check">
                                <input type="checkbox" checked name="customer_view" value="1" class="form-check-input" id="customer_view" value="{{ old('customer_view') }}">
                                <label class="form-check-label" for="customer_view">{{'Customer can track'}}</label>
                            </div>
                        </div>

                        <div class="pb-2">Please upload the consignment numbers in Excel<span class="text-danger">*</span></div>
                        <div id="successMessage" class="text-success new-success" hidden>File Uploaded Successfully</div>
                        <div class="custom-file pb-3 d-block">
                            <input required type="file" class="custom-file-input" id="customFile" name="excel" accept=".xlsx">
                            <label class="custom-file-label" for="customFile">Choose file</label></label>
                            <span class="mx-sm-2 text-muted pb-3">Supported Format: .xlsx</span>
                            <span class="float-right mx-sm-2"><a href="{{ route('download-manifest-sample') }}">Download sample file <i class="fa fa-file-excel-o" aria-hidden="true"></i></a></span>
                        </div>

                    </div>
                </div>
                <div class="col-8 mx-auto text-center">
                    <div class="w-50 mx-auto">
                        <button type="submit" class="mx-auto btn-lg btn btn-primary">{{ 'Submit' }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        $('#customFile').on('change',function(){
            //get the file name
            let fileName = $(this).val();
            let cleanFileName = fileName.replace('C:\\fakepath\\', " ")
            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(cleanFileName);
            if (cleanFileName !== '') {
                $("#successMessage").prop('hidden', false);
            } else {
                $("#successMessage").prop('hidden', true);
            }

        });

        function officeType () {
            return $("#office_type").val();
        }

        $("#receiver_id").on('change', function () {
            $("#receiver_id option:not(:last)").remove();
        });
        $('#receiver_id').select2({
            placeholder: "Choose Receiver ID",
            allowClear: true,
            minimumInputLength: 3,
            width: '200px',
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
            }
        });
    </script>

@endsection
