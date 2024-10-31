@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>@if($manifest_type== 'I') Incoming @elseif($manifest_type == 'O') Outgoing @endif Manifests</span></h1>

                </div>
                <div>
                    <a class="btn btn-primary" href=" @if($manifest_type == 'I'){{ route('manifests.incoming.create') }} @elseif($manifest_type == 'O') {{ route('manifests.outgoing.create') }} @endif"> 
                        Add New Manifest</a>
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

                            <div class="filter_form">
                           <!-- <form class="form-inline" action="{{ route('manifests.index') }}">
                                <div class="form-group mr-sm-2 mb-2">
                                    <select class="form-control" id="manifest_type" name="manifest_type">
                                        <option value=''>Select Type</option>
                                        <option value='I'>Incoming</option>
                                        <option value='O'>Outgoing</option>
                                    </select>
                                   
                                </div>

                                <button type="submit" class="btn btn-primary mr-3  mb-2">
                                            {{ __('Search') }}
                                        </button>
                                <a title="Reset" href="{{route('manifests.index')}}" class="btn btn-group  mb-2 btn-outline-dark">Reset</a>

                            </form> -->

                        </div>

                            <div class="card">
                            <table class="table table-bordered">
                                <tr>
                                    <th>No </th>
                                    <th>Consignment Number</th>
                                    <th>Origin</th>
                                    <th>Destination</th>
                                    <th>Sender Branch</th>
                                    <th>Receiver Branch</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                
                                @foreach ($manifests as $manifest)
                                    <tr>
                                        <td>
                                            {{$loop->iteration}}
                                        </td>
                                        <td>{{ $manifest->manifest_number }}</td>
                                        <td>{{ $manifest->origin_branch_id }}</td>
                                        <td>{{ $manifest->dest_branch_id }}</td>
                                        <td>@if($manifest->sender_type == 'HO' || $manifest->sender_type == 'BR')
                                             {{ $manifest->sender_branch->code}}
                                             @elseif($manifest->sender_type == 'FR')
                                             {{ $manifest->sender_franchisee->code }}
                                             @endif
                                        </td>
                                        <td>@if($manifest->receiver_type == 'HO' || $manifest->receiver_type == 'BR')
                                             {{ $manifest->receiver_branch->code}}
                                             @elseif($manifest->receiver_type == 'FR')
                                             {{ $manifest->receiver_franchisee->code }}
                                             @endif</td>
                                        <td>{{ date('d-m-Y H:i:s', strtotime($manifest->created_at)) }}</td>
                                       
                                        <td>
                                            <form action="{{ route('manifests.destroy',$manifest->id) }}"
                                                  method="POST">

                                                <a class="btn btn-secondary btn-sm"
                                                   href="{{ route('manifests.edit',$manifest->id) }}">Edit</a>

                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            </div>
                            {!! $manifests->links() !!}
                        </div>
                    </div>

            </div>
        </div>
    </div>







@endsection
