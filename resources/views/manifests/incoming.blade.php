@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h1 class="sb-page-header-title"><span>Incoming Manifests</span></h1>

                </div>
                <div>
                    <a class="btn btn-primary" href=" {{ route('manifests.incoming.create') }}"> 
                        Add Incoming Manifest</a>
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
                                        <td>
                                        @if(isset($manifest->booking->pincode->pincode))
                                        {{ $manifest->booking->pincode->pincode }}
                                        @endif
                                        </td>
                                        <td>
                                        @if(isset($manifest->booking->delivery->pincode->pincode))
                                        {{ $manifest->booking->delivery->pincode->pincode }}
                                        @endif
                                        </td>
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
                                           

                                                <a class="btn btn-secondary btn-sm"
                                                   href="{{ route('manifests.incoming_edit',$manifest->id) }}">Edit</a>

                                           
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
