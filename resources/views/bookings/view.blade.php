@extends('layouts.app')

@section('content')

    <div class="container">
        <!-- Page Header -->
        <div class="sb-page-header-content py-5">
            <div class="d-flex justify-content-between">
                <div>
                    <h4>Booking Details</h4>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('bookings.index') }}">View Bookings</a>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="profile-card section">
            <div class="row">
                <div class="col-md-9">
                    <div class="row mb-3">
                        <div class="col-6">
                            <b>{{ __('Consignment Number') }}</b> - {{ $booking->consg_number ?? '-' }}
                        </div>
                        <div class="col-6">
                            <b>{{ __('Consignment Type') }}</b> - {{ $booking->consg_type ?? '-' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <b>{{ __('Customer Name') }}</b> - {{ $booking->customer->customer_name ?? '-' }}
                        </div>
                        <div class="col-6">
                            <b>{{ __('Receiver Name') }}</b> - {{ $booking->delivery->receiver_name ?? '-' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <b>{{ __('Address') }}</b> -
                            {{ $booking->add_line_1 ?? '-' }}
                            <div class="address-cls">
                                {{ $booking->city ?? '-' }} <br>
                                {{ $booking->state ?? '-' }}
                            </div>
                        </div>
                        <div class="col-6">
                            <b>{{ __('Address') }}</b> -
                            {{ $booking->delivery->add_line_1 ?? '-' }}
                            <div class="address-clss">
                                {{ $booking->delivery->city ?? '-' }} <br>
                                {{ $booking->delivery->state ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <b>{{ __('Origin') }}</b> - {{ $booking->origin_pincode ?? '-' }}
                        </div>
                        <div class="col-6">
                            <b>{{ __('Destination') }}</b> - {{ $booking->delivery->dest_pincode ?? '-' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <b>{{ __('Received By') }}</b> -
                            {{ $booking->delivery->rec_name ?? '-' }} ({{ $booking->delivery->tookstatus ?? '-' }})
                        </div>
                        @if ($booking->status == 'Returned' && isset($booking->returnReason->reason->name))
                            <div class="col-6">
                                <b>{{ __('Reason') }}</b> - {{ $booking->returnReason->reason->name }}
                            </div>
                        @endif
                    </div>

                    <!-- Receiver Media -->
                    <div class="row mb-3">
                        @if (!empty($booking->delivery->receiverImageUrl[0]->url))
                            <div class="col-md-4">
                                <b>Receiver Photo</b>
                                <div>
                                    <label>Rotate Image:</label>
                                    <input type="button" class="btnRotate" value="90" onclick="rotateImage(90);" />
                                    <input type="button" class="btnRotate" value="-90" onclick="rotateImage(-90);" />
                                    <input type="button" class="btnRotate" value="180" onclick="rotateImage(180);" />
                                    <input type="button" class="btnRotate" value="360" onclick="rotateImage(360);" />
                                </div>
                                <div id="pop">
                                    <img id="imageresource" style="width:100%;" src="{{ url($booking->delivery->receiverImageUrl[0]->url) }}" class="transform" />
                                </div>
                            </div>
                        @endif

                        @if (!empty($booking->delivery->receiverSignUrl[0]->url))
                            <div class="col-md-4">
                                <b>Receiver Signature</b>
                                <div>
                                    <label>Rotate Image:</label>
                                    <input type="button" class="btnRotate" value="90" onclick="rotateSign(90);" />
                                    <input type="button" class="btnRotate" value="-90" onclick="rotateSign(-90);" />
                                    <input type="button" class="btnRotate" value="180" onclick="rotateSign(180);" />
                                    <input type="button" class="btnRotate" value="360" onclick="rotateSign(360);" />
                                </div>
                                <div id="pop1">
                                    <img id="signresource" style="width:100%;" src="{{ url($booking->delivery->receiverSignUrl[0]->url) }}" class="transform" />
                                </div>
                            </div>
                        @endif

                        @if (!empty($booking->delivery->receiverVoiceUrl[0]->url))
                            <div class="col-md-4">
                                <b>Receiver Voice</b>
                                <audio controls>
                                    <source src="{{ url($booking->delivery->receiverVoiceUrl[0]->url) }}" type="audio/ogg">
                                    <source src="{{ url($booking->delivery->receiverVoiceUrl[0]->url) }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="col-md-3">
                    <div><b>{{ __('Plan') }}</b> - {{ $booking->subs_name ?? '-' }}</div>
                    <div><b>{{ __('Booking Date') }}</b> - {{ date('d-m-Y', strtotime($booking->created_at)) }}</div>
                    <div>
                        <b>{{ __('Booked By') }}</b> -
                        @if (isset($booking->user))
                            {{ $booking->user->username ?? '' }}, {{ $booking->user->first_name ?? '' }} {{ $booking->user->last_name ?? '' }}
                            ({{ $booking->user->office->code ?? '' }})
                        @endif
                    </div>
                    <div>
                        @if ($booking->status == 'Delivered')
                            <b>{{ __('POD') }}</b> -
                            <a target="_blank" href="{{ URL::to('/booking/acknowledgement/r-' . $booking->consg_number) }}">Click Here</a>
                        @endif
                    </div>
                    <div>
                        <b>{{ __('Shipper Copy') }}</b> -
                        <a target="_blank" href="{{ URL::to('/booking/acknowledgement/s-' . $booking->consg_number) }}">Click Here</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <img src="" id="imagepreview" style="width: 100%;" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#pop").on("click", function() {
                $('#imagepreview').attr('src', $('#imageresource').attr('src'));
                $('#imagemodal').modal('show');
            });
            $("#pop1").on("click", function() {
                $('#imagepreview').attr('src', $('#signresource').attr('src'));
                $('#imagemodal').modal('show');
            });
        });

        function rotateImage(degree) {
            $('#imageresource').css('transform', 'rotate(' + degree + 'deg)');
        }

        function rotateSign(degree) {
            $('#signresource').css('transform', 'rotate(' + degree + 'deg)');
        }
    </script>

    <style>
        .address-cls {
            margin-left: 75px;
        }
        .address-clss {
            margin-left: 75px;
        }
        .section {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
    </style>

@endsection
