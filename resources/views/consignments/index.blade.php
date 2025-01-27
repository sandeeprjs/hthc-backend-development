<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<link id="bsdp-css" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.standalone.css" rel="stylesheet">

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Consignments</span></h1>
                </div>
                <div>
                    <button onclick="window.location.href = '{{ route('consignments.create') }}'" class="btn btn-primary">{{ 'Generate Consignment' }}</button>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success">
                {!! session()->get('success') !!}
            </div>
        @endif

        <div class="row m-0">
            <form class="form-inline" action="{{ route('consignments.index') }}">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <select class="input-group-text" id="office_type" name="office_type">
                            <option selected disabled>Office Type</option>
                            <option value="HO">Head Office</option>
                            <option value="BR">Branch</option>
                            <option value="FR">Partner</option>
                        </select>
                    </div>
                    <select id="office_id_search" name="office_id" class="form-control @error('office_id') is-invalid @enderror">
                    </select>
                    @error('office_id')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-2 mx-sm-2" id="sandbox-container">
                    <div class="input-daterange input-group" id="datepicker">
                        <input type="text" class="input-sm form-control" name="start_date" placeholder="From Date" value="{{ request()->input('start_date') }}">
                        <input type="text" class="input-sm form-control" name="end_date" placeholder="To Date" value="{{ request()->input('end_date') }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mx-sm-2 mb-2">{{ 'Search' }}</button>
                <a href="{{ route('consignments.index') }}" class="btn mb-2 mx-sm-1 btn-light btn-outline-secondary">{{ 'Reset' }}</a>
            </form>

            <div class="card w-100">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Consignment Numbers</th>
                        <th scope="col">Office Type</th>
                        <th scope="col">Office Id</th>
                        <th scope="col">Sheet Qty.</th>
                        <th scope="col">Date</th>
                        <th scope="col">Total</th>
                        <th scope="col">Used</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $index = $consignments->firstItem();
                    @endphp
                    @foreach($consignments as $consignment)
                        <tr>
                            <th scope="row">{{ $index++ }}</th>
                            <td>{{ $consignment->minConsgNum . ' - ' . substr($consignment->maxConsgNum, -8) }}</td>
                            <td>{{ $consignment->office_type == 'FR' ? 'Partner' : ($consignment->office_type == 'BR' ? 'Branch' : 'Head Office') }}</td>
                            <td>{{ $consignment->office->code ?? 'N/A' }}</td>
                            <td>{{ ceil($consignment->count / 48) }}</td>
                            <td>{{ date('d-m-Y', strtotime($consignment->created_at)) }}</td>
                            <td>{{ $consignment->count }}</td>
                            <td>{{ $consignment->UsedConsignment }}</td>
                            <td>
                                @if($consignment->batch_id)
                                    <button onclick="window.open('{{ route('consignments.reprint', $consignment->batch_id) }}', '_blank')" class="btn btn-sm btn-secondary">Reprint</button>
                                    <button onclick="window.open('{{ route('consignments.export', $consignment->batch_id) }}', '_blank')" class="btn btn-sm">
                                        <img src="https://img.icons8.com/windows/32/000000/export-excel.png"/>
                                    </button>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {!! $consignments->links() !!}
        </div>
    </div>
    <script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        function officeType() {
            return $("#office_type").val();
        }

        $('#office_id_search').select2({
            placeholder: "Choose office ID",
            width: '180px',
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

        $('#sandbox-container .input-daterange').datepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
            todayHighlight: true,
            toggleActive: true,
            endDate: 'today'
        });
    </script>
@endsection
