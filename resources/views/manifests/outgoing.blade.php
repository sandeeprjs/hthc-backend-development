@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Outgoing Manifests</span></h1>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('manifests.outgoing.create') }}">
                        Add Outgoing Manifest
                    </a>
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
                                    <th>No</th>
                                    <th>Consignment Number</th>
                                    <th>Origin Pincode</th>
                                    <th>Destination Pincode</th>
                                    <th>Sender Branch</th>
                                    <th>Receiver Branch</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($manifests as $manifest)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $manifest->manifest_number ?? 'N/A' }}</td>
                                        <td>
                                            {{ optional(optional($manifest->booking)->pincode)->pincode ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ optional(optional(optional($manifest->booking)->delivery)->pincode)->pincode ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @switch($manifest->sender_type)
                                                @case('HO')
                                                @case('BR')
                                                    {{ optional($manifest->sender_branch)->code ?? 'N/A' }}
                                                    @break
                                                @case('FR')
                                                    {{ optional($manifest->sender_franchisee)->code ?? 'N/A' }}
                                                    @break
                                                @default
                                                    N/A
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($manifest->receiver_type)
                                                @case('HO')
                                                @case('BR')
                                                    {{ optional($manifest->receiver_branch)->code ?? 'N/A' }}
                                                    @break
                                                @case('FR')
                                                    {{ optional($manifest->receiver_franchisee)->code ?? 'N/A' }}
                                                    @break
                                                @default
                                                    N/A
                                            @endswitch
                                        </td>
                                        <td>
                                            {{ $manifest->created_at ? date('d-m-Y H:i:s', strtotime($manifest->created_at)) : 'N/A' }}
                                        </td>
                                        <td>
                                            <a class="btn btn-secondary btn-sm"
                                               href="{{ route('manifests.outgoing_edit', $manifest->id) }}">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No manifests found</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {!! $manifests->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
