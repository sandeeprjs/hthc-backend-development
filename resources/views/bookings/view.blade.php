@extends('layouts.app')




@section('content')


<div class="container">
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h4>Booking Details </h4>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('bookings.index') }}"> View Bookings</a>
                </div>
            </div>


        </div>
</div>

<div class="profile-card">
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-6">
                        <b>{{ __(' Consignment Number') }} </b> -
                        @if(isset($booking)) {{ $booking->consg_number }}  @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Consignment Type') }} </b> -
                        @if(isset($booking)) {{ $booking->consg_type  }}  @endif
                    </div>

                </div>
                <div class="row">
                    <div class="col-6">
                        <b>{{ __('Customer Name') }} </b> -
                        @if(isset($booking->customer->customer_name)) {{ $booking->customer->customer_name }}  @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Receiver Name') }} </b> -
                        @if(isset($booking)) {{ $booking->delivery->receiver_name  }}  @endif
                    </div>

                </div>
                <div class="row">
                    <div class="col-6">
                        <b>{{ __('Address') }} </b> -
                        @if(isset($booking))

                                         {{ $booking->add_line_1 }}
                                         <div class="address-cls">
                                            {{ $booking->city }}
                                            <br> {{ $booking->state }}
                                        </div>
                        @endif
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Address') }} </b> -
                        @if(isset($booking))

                                         {{ $booking->delivery->add_line_1 }}
                                         <div class="address-clss">
                                            {{ $booking->delivery->city }}
                                            <br> {{ $booking->delivery->state }}
                                        </div>
                        @endif
                    </div>


                </div>

                <div class="row">
                    <div class="col-6">
                        <b>{{ __('Origin') }} </b> -

                                {{ $booking->origin_pincode}}
                    </div>
                    <div class="col-6">
                        <b>{{ __(' Destination') }} </b> -

                                {{ $booking->delivery->dest_pincode }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                           <b>{{ __(' Received By ') }} </b> -
                           @if(isset($booking)) {{ $booking->delivery->rec_name  }} ( {{ $booking->delivery->tookstatus }} )  @endif
                    </div>

                    @if($booking->status == 'Returned')
                       @if(isset($booking->returnReason->reason->name))
                        <div class="col-6">
                            <b>{{ __(' Reason') }} </b> -

                                    {{ $booking->returnReason->reason->name }}
                        </div>
                        @endif
                    @endif


                </div>
                <div></div>
                <div class="row">

                        @if (count($booking->delivery->receiverImageUrl) != 0)
                        <div class="col-4">
                                <b>Receiver Photo</b>
                                <div>
                                    <label>Rotate Image:</label>
                                            <input type="button" class="btnRotate" value="90" onClick="rotateImage(this.value);" />
                                            <input type="button" class="btnRotate" value="-90" onClick="rotateImage(this.value);" />
                                            <input type="button" class="btnRotate" value="180" onClick="rotateImage(this.value);" />
                                            <input type="button" class="btnRotate" value="360" onClick="rotateImage(this.value);" />
                                    </div>
                                    <div id="pop">
                                                <img id="imageresource" style="width:45%;" src="{{ url($booking->delivery->receiverImageUrl[0]->url) }}" class="transform"/></td>
                                    </div>
                        </div>
                        @endif


                        @if(isset($booking->delivery->receiverSignUrl[0]->url))
                        <div class="col-4">
                                <b>Receiver Signature</b>

                                <div>
                                    <label>Rotate Image:</label>
                                            <input type="button" class="btnRotate" value="90" onClick="rotateSign(this.value);" />
                                            <input type="button" class="btnRotate" value="-90" onClick="rotateSign(this.value);" />
                                            <input type="button" class="btnRotate" value="180" onClick="rotateSign(this.value);" />
                                            <input type="button" class="btnRotate" value="360" onClick="rotateSign(this.value);" />
                                </div>
                                <div id="pop1">
                                    <img id="signresource" style="width:45%;" src="{{ url($booking->delivery->receiverSignUrl[0]->url) }}" class="transform"/></td>
                                </div>
                        </div>
                        @endif


                    @if(isset($booking->delivery->receiverVoiceUrl[0]->url))
                    <div class="col-4">
                            <b>Receiver Voice</b>
                            <div>
                            <audio controls>
                                    <source src="{{ url($booking->delivery->receiverVoiceUrl[0]->url) }}" type="audio/ogg">
                                    <source src="{{ url($booking->delivery->receiverVoiceUrl[0]->url) }}" type="audio/mpeg">
                                    <source src="{{ url($booking->delivery->receiverVoiceUrl[0]->url) }}" type="audio/mp3"></source>
                                    Your browser does not support the audio element.
                            </audio>
                            </div>
                    </div>
                    @endif


                </div>





            </div>
            <div class="col-md-3">
                       <div>
                           <b>{{ __(' Plan ') }} </b> -
                           @if(isset($booking)) {{ $booking->subs_name  }}  @endif
                       </div>
                       <div>
                           <b>{{ __(' Booking Date ') }} </b> -
                           @if(isset($booking))
                               {{ date('d-m-Y', strtotime($booking->created_at)) }}
                            @endif
                       </div>
                       <div>
                           <b>{{ __(' Booked By ') }} </b> -
                           @if(isset($booking->user->first_name))
                                {{  $booking->user->username. ','. $booking->user->first_name .' '.$booking->user->last_name . '( '.$booking->user->office->code.' )' }}
                            @endif
                       </div>

                       <div>

                            @if(isset($booking->delivery->delivery_user_id))
                               <b>{{ __( $booking->status .' By ') }} </b> -

                                {{ $booking->delivery->deliveryBranch->username .', '. $booking->delivery->deliveryBranch->first_name .' '.$booking->delivery->deliveryBranch->last_name .'( '.$booking->delivery->user->office->code.' )' }}
                            @endif
                       </div>
                       @if($booking->status == 'Delivered')
                            <div>
                            <b>{{ __(' POD - ') }} </b> -
                            <a target="blank" href={{ URL::to("/booking/acknowledgement/r-$booking->consg_number") }}> Click Here </a>
                            </div>
                       @endif
                            <div>
                            <b>{{ __(' Shipper Copy - ') }} </b> -
                            <a target="blank" href={{ URL::to("/booking/acknowledgement/s-$booking->consg_number") }}> Click Here </a>
                            </div>
                        @if($booking->batch_id)
                        <div>

                            <b>{{ __(' Bulk Shipper Copy - ') }} </b> -
                            <a target="blank" href={{ URL::to("/booking/acknowledgement/s-".$booking->batch_id * env('ENC_KEY')) }}> Click Here </a>
                            </div>
                        @endif





            </div>

        </div>

        <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                        <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        </div>
                        <div class="modal-body">
                        <img src="" id="imagepreview" style="width: 400px; " >
                        </div>

                </div>
            </div>
        </div>
    </div>

    <script>

$(document).ready(function() {
                $("#pop").on("click", function() {
                   $('#imagepreview').attr('src', $('#imageresource').attr('src')); // here asign the image to the modal when the user click the enlarge link
                   $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
                });
                $("#pop1").on("click", function() {
                   $('#imagepreview').attr('src', $('#signresource').attr('src')); // here asign the image to the modal when the user click the enlarge link
                   $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
                });

});
var x = document.getElementById("myAudio");

function playAudio() {
  x.play();
}

function pauseAudio() {
  x.pause();
}
function rotateImage(degree) {
	$('#imageresource').animate({  transform: degree }, {
    step: function(now,fx) {
        $(this).css({
            '-webkit-transform':'rotate('+now+'deg)',
            '-moz-transform':'rotate('+now+'deg)',
            'transform':'rotate('+now+'deg)'
        });
    }
    });

}
function rotateSign(degree) {
$('#signresource').animate({  transform: degree }, {
    step: function(now,fx) {
        $(this).css({
            '-webkit-transform':'rotate('+now+'deg)',
            '-moz-transform':'rotate('+now+'deg)',
            'transform':'rotate('+now+'deg)'
        });
    }
    });
}
</script>
<style>

img#demo-image {
    margin-top: 65px;
}
.address-cls{
    margin-left:75px;
}
.address-clss{
    margin-left:75px;
}
.reason-cls {
    margin-top: 100px;
}
</style>


@endsection
