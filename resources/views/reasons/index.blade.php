@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Reasons</span></h1>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('reasons.create') }}">Add New Reason</a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">

                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <p>{{ $message }}</p>
                                </div>
                            @endif
                            <div class="card">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>S. No</th>
                                    <th>Reason Code </th>
                                    <th> Reasons</th>
                                    <th> Reason Type</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($reasons))
                                @php
                                $index = $reasons->firstItem()
                                @endphp

                                @foreach ($reasons as $reason)
                                    <tr>
                                        <th>{{ $index++ }}</th>
                                        <th>{{ $reason->code }}</th>
                                        <td>{{ $reason->name }}</td>
                                        <td>{{ ucfirst($reason->type) }}</td>
                                        <td>
                                            <div class="btn-group">
                                            <a title="Edit" href="{{ route('reasons.edit',$reason->id)}}" class="btn btn-secondary btn-sm">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    
@endsection
