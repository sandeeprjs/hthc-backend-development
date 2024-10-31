@extends('layouts.app')

@section('content')
<div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                   
                </div>

            </div>

        </div>
        <div class="row">
            <div class="col-8">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                    <h1 class="sb-page-header-title"><span>Tracking</span></h1>
                        <form action="{{ route('tracking.index') }}" method="GET">
                           
                            <div class="row">
                                <div class="input-group mb-3 col-md-6">
                                <input type="text" name="consign_number" class="form-control @if(isset($error)) is-invalid @endif" placeholder="Consignment Number" aria-label="Consignment Number" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Track</button>
                                </div>
                                @if(isset($error))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $error }}</strong>
                                        </span>
                                @endif
                                        
                            </div>


                                <!-- <div class="col-xs-12 col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Consignment Number<span class="text-danger">*</span></label>
                                        <input type="text" required name="consign_number" class="form-control  @if(isset($error)) is-invalid @endif" placeholder="Consignment Number" value="{{ $request->consign_number }}">
                                        @if(isset($error))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $error }}</strong>
                                        </span>
                                        @endif
                                        <button type="submit" class="btn btn-primary">{{ 'Track' }}</button>
                                    </div>

                                </div>
                                -->
                             
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <br>
        @if(isset($booking))
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
                            <div> {{ $booking->status }}  </div>
                         </td>
                        <td>  
                            <b> Date Time </b>
                            <div>@if($booking->status == 'Booked & Dispatched')
                                   {{ date('d-m-Y H:i:s',strtotime($booking->created_at)) }}
                                 @else
                                    {{ date('d-m-Y H:i:s',strtotime($booking->updated_at)) }}
                                 @endif  </div>
                        </td>
                   </tr>
                   <tr> 
                        <td> 
                            <b> Origin </b> 
                            <div>
                                
                                @if($booking->add_line_1) 
                                    {{ $booking->add_line_1 }} - 
                                @endif
                                {{ $booking->origin_pincode}} 
                            </div>
                        </td>
                        <td>
                            @if($booking->status == 'Returned')
                                @if(isset($booking->returnReason->reason->name))
                                    <b>{{ __(' Reason') }} </b> <br>
                                            {{ $booking->returnReason->reason->name }}
                                @endif
                            @endif
                        </td>
                        <td></td>
                   </tr>
                   <tr> 
                        <td> 
                            <b> Destination </b> 
                            <div>
                                @if($booking->delivery->receiver_name)
                                    {{ $booking->delivery->receiver_name}},<br>
                                @endif
                                @if($booking->delivery->add_line_1) 
                                    {{ $booking->delivery->add_line_1 }}, <br>
                                @endif
                                @if($booking->delivery->add_line_2)
                                    {{ $booking->delivery->add_line_2 }}, <br>
                                @endif
                                {{ $booking->delivery->dest_pincode }}.
                            </div>
                        </td>
                        <td>
                        @if($booking->status == 'Returned')
                            @if(isset($booking->returnReason->reason->name))
                                    <div class="col-6">
                                        <b>{{ __(' Reason') }} </b> -
                                                
                                                {{ $booking->returnReason->reason->name }}
                                    </div>
                            @endif
                        @endif
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
                                               
                                                 @if(Auth::check())
                                                    @if (Auth::user()->isAdmin())
                                                        <td> <b> Employee </b></td>
                                                    @endif
                                                @endif
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
                                                 @if(Auth::check())
                                                    @if (Auth::user()->isAdmin())
                                                    <td>{{ $booking->user->first_name}} {{ $booking->user->last_name }}</td>
                                                 @endif
                                                @endif
                                            </tr>
                                            
                                            @if(isset($tracking))
                                                @foreach($tracking as $track)
                                                    <tr>
                                                        <td> {{ date('d-m-Y H:i:s',strtotime($track->created_at)) }} </td>
                                                        <td> {{ $track->status}} </td>
                                                        <td>
                                                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                        {{ $track->receiver_type }} @if($track->receiver_type == 'HO' || $track->receiver_type == 'BR')
                                                                {{ $track->receiver_branch->branch_name}} 
                                                             @elseif($track->receiver_type == 'FR')
                                                                {{ $track->receiver_franchisee->enterprise_name }}
                                                             @endif
                                                        </td>
                                                        @if(Auth::check())
                                                            @if (Auth::user()->isAdmin())
                                                                <td>
                                                                    @if(isset($track->user->first_name))
                                                                    {{ $track->user->first_name}} {{ $track->user->last_name }}
                                                                    @endif
                                                                    </td>
                                                            @endif
                                                        @endif
                                                      
                                                       
                                                    </tr>
                                                @endforeach
                                            @endif
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
                                                        @if(Auth::check())
                                                        @if (Auth::user()->isAdmin())
                                                           
                                                            <td>
                                                            @if(isset($delivery->deliveryBranch->first_name))
                                                            {{ $delivery->deliveryBranch->first_name }} {{ $delivery->deliveryBranch->last_name }}
                                                            @endif
                                                            </td>
                                                        @endif
                                                    @endif
                                                    </tr>
                                            @endif                                           
                                        </table>
                                </div>
                            </div>
                    </div>
                </div>
           
        </div>
        @endif
        

    </div>
<style>
.panel-heading {
    border: 1px solid #dee2e6;
    background: gray;
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
    background: gray;
    color:white;
}
</style>
@endsection