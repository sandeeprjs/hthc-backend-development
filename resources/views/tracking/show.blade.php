@extends('layouts.app')

@section('auth-content')
        <div class="row">
                <div class="col-8">
                    <h5 class=" pt-4"><span>Tracking Results</span></h5>
                </div>
                <div class="col-4 text-right">
                    <div class="logo1">
                        <img src="{{URL::to('/')}}/images/logo.png" style=" width: 90px; margin-top: 25px;" />
                    </div>
                </div>
        </div>
        <div class="row pt-5">
            <div class="col-12">
                <div class="row justify-content-center">
                    <div class="col-md-12">

                        <form action="{{ route('public.tracking') }}" method="GET">

                            <div class="row">
                                <div class="input-group mb-3 col-md-6">
                                    <input type="text" name="consg_number" class="form-control @error('consg_number') is-invalid @enderror" placeholder="Consignment Number" aria-label="Consignment Number" aria-describedby="basic-addon2" value="{{ old('consg_number') ?? request()->input('consg_number') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">Track</button>
                                    </div>
                                    @error('consg_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if($booking)
            <div class="card w-100">
                <table class="table table-bordered">
                    <tr>
                        <th class="tb_head" colspan="3"> Shipment Details</th>
                    </tr>
                    <tr>
                        <td>
                            <b> AWB / Ref. No. </b>
                            <div>{{ $booking->consg_number }}  </div>
                        </td>
                        <td>
                            <b> Status </b>
                            <div>{{ $booking->status }}  </div>
                        </td>
                        <td>
                            <b> Date Time </b>
                            <div>{{ date('d-m-Y H:i:s',strtotime($booking->created_at)) }}  </div>
                        </td>
                    </tr>
                    <tr>
                        <td> <b> Sender </b>
                            <div>
                                 @if(isset($booking->customer->customer_name))
                                     {{ $booking->customer->customer_name }}
                                 @endif
                            </div>
                        </td>
                        <td>
                            <b> Origin Address</b>
                            <div>
                                @if($booking->add_line_1)
                                    {{ $booking->add_line_1 }} -
                                @endif
                                {{ $booking->origin_pincode}}
                            </div>
                        </td>
                        
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <b>Receiver</b>
                            <div>
                                @if(isset($delivery->receiver_name))
                                   {{ $delivery->receiver_name }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <b> Destination Address</b>
                            <div>

                                @if($booking->delivery->add_line_1)
                                    {{ $booking->delivery->add_line_1 }} -
                                @endif
                                {{ $booking->delivery->dest_pincode }}
                            </div>
                        </td>
                        
                        <td></td>
                    </tr>
                </table>

            </div>

            <div class="panel-group" id="accordion"> <!-- accordion 1 -->
                <div class="panel panel-primary">
                    <div class="panel-heading"> <!-- panel-heading -->
                        <!-- title 1 -->
                        <a data-toggle="collapse" data-parent="#accordion" href="#accordionOne">
                            Show Shipment Travel History
                        </a>

                    </div>
                    <!-- panel body -->
                    <div id="accordionOne" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table table-bordered">
                                <tr>
                                    <td> <b> Date </b> </td>
                                    <td> <b> Activity </b> </td>
                                    <td> <b> Location </b> </td>
                                </tr>
                                <tr>
                                                 <td> {{ date('d-m-Y H:i:s',strtotime($booking->created_at))}} </td>
                                                 <td> Booked & Dispatched </td>
                                                 <td>
                                                 <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                 @if($booking->origin_office_type == 'HO' || $booking->origin_office_type == 'BR')
                                                         {{ $bookingOffice->branch_name }} 
                                                      @elseif($booking->origin_office_type == 'FR')
                                                         {{ $bookingOffice->enterprise_name }}
                                                      @endif
                                                 </td>
                                                
                                            </tr>
                                @foreach($tracking as $track)
                                    <tr>
                                        <td> {{ date('d-m-Y H:i:s',strtotime($track->created_at)) }} </td>
                                        <td> {{ $track->status}} </td>
                                        <td>
                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                            {{ $track->receiver_type }}
                                            @if($track->receiver_type == 'HO' || $track->receiver_type == 'BR')
                                                {{ $track->receiver_branch->branch_name}}
                                            @elseif($track->receiver_type == 'FR')
                                                {{ $track->receiver_franchisee->enterprise_name }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if($booking->status == 'Delivered')
                                        <tr>
                                                    <td> {{ date('d-m-Y H:i:s',strtotime($delivery->updated_at)) }} </td>
                                                    <td> {{ $booking->status}} </td>
                                                    <td>
                                                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                    @if(isset($delivery->deliveryBranch->branch))
                                                    {{ $delivery->deliveryBranch->branch->branch_type }}       
                                                    {{ $delivery->deliveryBranch->branch->branch_name }} 
                                                    @endif
                                                    </td>
                                                   
                                        </tr>
                                @endif    
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    <style>




        .panel-heading {
            border: 1px solid #000;
            background: #181C93;
            padding: 10px;
        }
        .panel-heading a {
            color: white;
        }
        div#accordion {
            margin-top: 10px;
            background: white;

        }
        th.tb_head {
            background: #181C93;
            color:white;
        }

    </style>
@endsection
