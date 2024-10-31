@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Bulk Incoming Manifest Import</span></h1>
                </div>
                <a class="btn btn-primary" href="{{ route('manifests.incoming.create') }}"> Normal Incoming Manifests</a>
            </div>
        </div>
        <form method="POST" action="{{ route('manifests.import.incoming.create') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-8 mt-4" >
                    <div class="booking-card pb-5">
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

                        {{--                                <label for="exampleFormControlFile1">Upload Document<span class="text-danger">*</span></label>--}}
                        {{--                                <input type="file" required class="form-control-file" id="exampleFormControlFile1" name="excel">--}}
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
    </script>

@endsection
