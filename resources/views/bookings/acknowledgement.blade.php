@extends('layouts.app')

@section('auth-content')

    @php
        $ackKey = \Illuminate\Support\Facades\Request::segment('3')
    @endphp

    <div class="no-print py-4">
      @if(count($bookings) == 0)
          <div class="h5 p-4 text-center">No record found!</div>
      @else
        <div class="text-center">
            <h5>Please click the below button to print the acknowledgement.</h5>
            <button id="printButton" onclick="window.print()" class="btn btn-success">Print</button>
        </div>
      @endif
    </div>

    <div id="printArea">
        @if(substr($ackKey, 0, 1) == 's' )
        <div class="slipper-copy mb-5">
                @foreach($bookings as $booking)
                <table cellspacing="0" cellpadding="0" class="table-bordered" width="100%" valign="top" style="margin-bottom: 20px">
                    <tbody>
                    <tr>
                        <td width="50%" valign="top">
                            <h3 class="t-title" style="background-color: red">SHIPPER</h3>
                            <table  cellspacing="0" cellpadding="0" class="t-border" border="0" width="100%">
                                <tr>
                                    <td>
                                        <p>NAME/DEPT: {{ $booking->customer_name }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 50px" valign="top">
                                        <p>ADDRESS: {{ $booking->add_line_1.' '.$booking->add_line_2 }}</p>
                                        <p>{{ $booking->city.', '.$booking->state }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>TEL: {{ $booking->mobile_number }}</p>
                                    </td>
                                    <td>
                                        <p>PIN: {{ $booking->origin_pincode }}</p>
                                    </td>
                                </tr>

                            </table>

                            <table cellspacing="0" cellpadding="0" width="100%" style="table-layout: fixed ;">
                            
                                <tr>
                                    <td><p>Origin</p></td>
                                    <td><p>Destination</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>{{ $booking->origin_pincode }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $booking->delivery->dest_pincode }}</p>
                                    </td>
                                </tr>
                            </table>

                            <table cellspacing="0" cellpadding="0" class="table-bordered" width="100%">
                                <tbody>
                                <tr>
                                    <td>
                                        <h3 class="t-title">
                                            COURIER CONSIGNMENT NOTE
                                        </h3>
                                        <table cellspacing="0" cellpadding="0" class="" border="0" width="100%">
                                            <tr>
                                                <td width="20%">
                                                    <p>Booking</p>
                                                </td>
                                                <td><p>Branch</p>
                                                </td>
                                                <td>
                                                    <p>Name/Code</p>
                                                </td>
                                                <td><p>Dox</p></td>
                                            </tr>
                                            <tr>
                                                <td><p>1</p></td>
                                                <td><p>{{ isset($booking->user->office->code) ? $booking->user->office->code : '' }}</p></td>
                                                <td><p>
                                                @if($booking->origin_office_type == 'BR' || $booking->origin_office_type == 'HO' )
                                                     {{ $booking->office->branch_name }}
                                                @endif
                                                @if($booking->office_type == 'FR')
                                                     {{ $booking->office->enterprise_name }}
                                                @endif
                                                </p>
                                                </td>
                                                <td><p>{{ $booking->consg_type }}</p></td>
                                            </tr>
                                        </table>



                                        <table cellspacing="0" cellpadding="0" class="text-center p-small" border="0" width="100%">
                                            <tr>
                                                <td>
                                                    <h3 class="t-title">
                                                        OUR LIABILITY FOR ANY LOSS OR DAMAGE TO THE SHIPMENT IS LIMITED TO RS. 100/- ONLY
                                                    </h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="p-small">BOOKING OF CURRENCY / JEWELLERY IS BANNED,
                                                    SHARE CERTIFICATES with blank share transfer from are not allowed,
                                                        Claims should be preferred & settled within 30 days of booking centre only
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="p-small">I/We certify that this consignment does not contain personal mail which Infringes Indian Postal Act nor any Cash/Jewellery, contraband drugs or any prohibited items as per Central/State/Local Authorities.<br/>
                                                        I/We undertake to pay all Central/State/Local levels payable on this consignment.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table cellspacing="0" cellpadding="0" class="table-bordered" width="100%" style="table-layout: fixed ;">
                                <tr>
                                    <td><p>Sender Signature</p></td>
                                    <td><p>Date</p></td>
                                    <td><p>Time</p></td>
                                    <td><p>For HTHC PVT. LTD.</p></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><p>{{ date('d-m-Y', strtotime($booking->created_at)) }}</p></td>
                                    <td><p>{{ date('H:i', strtotime($booking->created_at)) }}</p></td>
                                    <td><p></p></td>
                                </tr>
                            </table>

                        </td>
                        <td width="50%" valign="top">
                            <h3 class="t-title">RECEIVER</h3>
                            <table cellspacing="0" cellpadding="0" class="t-border" border="0" width="100%">
                                <tr>
                                    <td>
                                        <p>NAME/DEPT: {{ $booking->delivery->receiver_name }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 50px" valign="top">
                                        <p>ADDRESS: {{ $booking->delivery->add_line_1.' '.$booking->delivery->add_line_2 }}</p>
                                        <p>{{ $booking->delivery->city.', '.$booking->delivery->state }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>TEL: {{ $booking->delivery->mobile_number }}</p>
                                    </td>
                                    <td>
                                        <p>PIN: {{ $booking->delivery->dest_pincode }}</p>
                                    </td>
                                </tr>

                            </table>

                            <table cellspacing="0" cellpadding="0" width="100%" style="table-layout: fixed ;">
                                <tr>
                                    <td><p>Plan</p></td>
                                    <td><p>{{ $booking->subs_name }}</p></td>
                                </tr>
                                <tr>
                                    <td><p>Weight</p></td>
                                    <td><p>Amount(Inc. GST)</p></td>
                                </tr>
                                <tr>
                                    <td><p>{{ $booking->final_weight ?? ($booking->consg_type == 'dox' ? $booking->weight : $booking->vol_weight) }} Kg</p></td>
                                    <td><p>Rs. {{ $booking->final_amount ?? $booking->booked_amount }}</p></td>
                                </tr>
                            </table>

                            <table width="100%" class="text-center" style="height: 68px;">
                                <tr>
                                    <td>
                                        <img src="data:image/png;base64, {{ \Milon\Barcode\DNS1D::getBarcodePNG($booking->consg_number, "C128",1.5,50,array(1,1,1)) }}" alt="barcode"/>
                                        <h4>{{ $booking->consg_number }}</h4>
                                    </td>
                                </tr>
                            </table>





                            <table cellspacing="0" cellpadding="0"  class="text-center p-small" border="0" width="100%">
                                <tr>
                                    <td>
                                        <h3 class="t-title">
                                            THANKS FOR USING OUR SERVICES
                                        </h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="p-small">
                                            We have the right to accept or refuse the booking of any document or parcel. This is a Non-Negotiable consignment note subject to the terms & conditions of contract. A copy of which is available at the regd. office of HTHC (P) LTD., Who shall not be liable for special incidental or consequential damages
                                            arising from the carriage hereof. HTHC (P) Ltd., disclaims all warranties expressed or implied with respect to this shipment for any loss or damages. Limits liability to mix of a Rs. 100/- (Rs. One Hundered only) for any case per consigment. All enquiries to be made to the booking center in writing within 30 days from the date of booking.
                                        </p>
                                    </td>
                                </tr>

                            </table>

                            <table cellspacing="0" cellpadding="0" width="100%">
                                <tr>
                                    <td width="60%">
                                        <p>
                                           No 9/2, Banashankari Complex, 8th A Main Road, Sampangiram Nagar, Hudson Circle, Bangalore - 560027 <br>
                                           PH (080 22292470 / 71).
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <img class="logo-check" src="{{URL::to('/')}}/images/logo.png" />
                                    </td>
                                </tr>


                            </table>



                        </td>
                    </tr>
                    </tbody>
                </table>
                @endforeach

        </div>
        @else
        <div class="slipper-copy slipper-copy-r mt-5">
            <table cellspacing="0" cellpadding="0" class="table-bordered" width="100%" valign="top">
                <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td width="50%" valign="top">
                            <h3 class="t-title">SHIPPER</h3>
                            <table  cellspacing="0" cellpadding="0" class="t-border" border="0" width="100%">
                                <tr>
                                    <td>
                                        <p>NAME/DEPT: {{ $booking->customer_name }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 50px" valign="top">
                                        <p>ADDRESS: {{ $booking->add_line_1.' '.$booking->add_line_2 }}</p>
                                        <p>{{ $booking->city.', '.$booking->state }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>TEL: {{ $booking->mobile_number }}</p>
                                    </td>
                                    <td>
                                        <p>PIN: {{ $booking->origin_pincode }}</p>
                                    </td>
                                </tr>

                            </table>

                            <table cellspacing="0" cellpadding="0" width="100%" style="table-layout: fixed ;">
                                <tr>
                                    <td><p>Origin</p></td>
                                    <td><p>Destination</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>{{ $booking->origin_pincode }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $booking->delivery->dest_pincode }}</p>
                                    </td>
                                </tr>
                            </table>

                            <table cellspacing="0" cellpadding="0" class="table-bordered" width="100%">
                                <tbody>
                                <tr>
                                    <td>
                                        <h3 class="t-title">
                                            COURIER CONSIGNMENT NOTE
                                        </h3>
                                        <table cellspacing="0" cellpadding="0" class="" border="0" width="100%">
                                            <tr>
                                                <td width="20%">
                                                    <p>Booking</p>
                                                </td>
                                                <td><p>Branch</p>
                                                </td>
                                                <td>
                                                    <p>Name/Code</p>
                                                </td>
                                                <td><p>Dox</p></td>
                                            </tr>
                                            <tr>
                                            <td><p>1</p></td>
                                                <td><p>{{ isset($booking->user->office->code) ? $booking->user->office->code : '' }}</p></td>
                                                <td><p>
                                                @if($booking->origin_office_type == 'BR' || $booking->origin_office_type == 'HO' )
                                                     {{ $booking->office->branch_name }}
                                                @endif
                                                @if($booking->office_type == 'FR')
                                                     {{ $booking->office->enterprise_name }}
                                                @endif
                                                </p>
                                                </td>
                                                <td><p>{{ $booking->consg_type }}</p></td>
                                            </tr>
                                        </table>



                                        <table cellspacing="0" cellpadding="0" class="text-center p-small" border="0" width="100%">
                                            <tr>
                                                <td>
                                                    <h3 class="t-title">
                                                        OUR LIABILITY FOR ANY LOSS OR DAMAGE TO THE SHIPMENT IS LIMITED TO RS. 100/- ONLY
                                                    </h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="p-small">BOOKING OF CURRENCY / JEWELLERY IS BANNED,
                                                        SHARE CERTIFICATES with blank share transfer from are not allowed,
                                                        Claims should be preferred & settled within 30 days of booking centre only
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="p-small">I/We certify that this consignment does not contain personal mail which Infringes Indian Postal Act nor any Cash/Jewellery, contraband drugs or any prohibited items as per Central/State/Local Authorities.<br/>
                                                        I/We undertake to pay all Central/State/Local levels payable on this consignment.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table cellspacing="0" cellpadding="0" class="table-bordered" width="100%" style="table-layout: fixed ;">
                                <tr>
                                    <td><p>Sender Signature</p></td>
                                    <td><p>Date</p></td>
                                    <td><p>Time</p></td>
                                    <td><p>For HTHC PVT. LTD.</p></td>
                                </tr>
                                <tr>
                                    <td><p></p></td>
                                    <td><p>{{ date('d-m-Y', strtotime($booking->created_at)) }}</p></td>
                                    <td><p>{{ date('H:i', strtotime($booking->created_at)) }}</p></td>
                                    <td><p></p></td>
                                </tr>
                            </table>

                        </td>
                        <td width="50%" valign="top">
                            <h3 class="t-title">RECEIVER</h3>
                            <table cellspacing="0" cellpadding="0" class="t-border" border="0" width="100%">
                                <tr>
                                    <td>
                                        <p>NAME/DEPT: {{ $booking->delivery->receiver_name }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 50px" valign="top">
                                        <p>ADDRESS: {{ $booking->delivery->add_line_1.' '.$booking->delivery->add_line_2 }}</p>
                                        <p>{{ $booking->delivery->city.', '.$booking->delivery->state }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>TEL: {{ $booking->delivery->mobile_number }}</p>
                                    </td>
                                    <td>
                                        <p>PIN: {{ $booking->delivery->dest_pincode }}</p>
                                    </td>
                                </tr>

                            </table>

                            <table cellspacing="0" cellpadding="0" width="100%" style="table-layout: fixed ;">
                                <tr>
                                    <td><p>Plan</p></td>
                                    <td><p>{{ $booking->subs_name }}</p></td>
                                </tr>
                                <tr>
                                    <td><p>Weight</p></td>
                                    <td><p>Amount(Inc. GST)</p></td>
                                </tr>
                                <tr>
                                    <td><p>{{ $booking->final_weight ?? ($booking->consg_type == 'dox' ? $booking->weight : $booking->vol_weight) }} Kg</p></td>
                                    <td><p>Rs. {{ $booking->final_amount ?? $booking->booked_amount }}</p></td>
                                </tr>
                            </table>

                            <table width="100%" class="text-center" style="height: 68px; padding-top: 20px">
                                <tr>
                                    <td>
                                        <img src="data:image/png;base64, {{ \Milon\Barcode\DNS1D::getBarcodePNG($booking->consg_number, "C128",1,40,array(1,1,1)) }}" alt="barcode"/>
                                        <h4>{{ $booking->consg_number }}</h4>
                                    </td>
                                </tr>
                            </table>





                            <table cellspacing="0" cellpadding="0"  class=" p-small" border="0" width="100%">
                                <tr>
                                    <td>
                                        <h3 class="t-title text-center">
                                            Received in Good Order and Condition
                                        </h3>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%">
                                <tr>
                                    <td width="50%">
                                        <p>Receiver Name:  @if(isset($booking)) {{ $booking->delivery->rec_name  }} ( {{ $booking->delivery->tookstatus }} )  @endif</p>
                                        <div class="d-flex">
                                            <p>Date & Time: {{ $booking->delivery->delivery_datetime ? date('d-m-Y H:i', strtotime($booking->delivery->delivery_datetime)): '' }}</p>
                                        </div>

                                    </td>

                                    @if (count($booking->delivery->receiverImageUrl) != 0)
                                        <td><p>Receiver Photo</p>
                                        <img src="{{ url($booking->delivery->receiverImageUrl[0]->url) }}" class="transform"/>
                                        </td>
                                    @endif    
                                    @if(isset($booking->delivery->receiverSignUrl[0]->url))
                                        <td><p>Receiver Signature</p>
                                        <img src="{{ url($booking->delivery->receiverSignUrl[0]->url) }}" class="transform"/></td>
                                    @endif
                                </tr>
                            </table>

                            <table cellspacing="0" cellpadding="0" width="100%">
                                <tr>
                                    <td width="60%">
                                        <p>
                                           No 9/2, Banashankari Complex, 8th A Main Road, Sampangiram Nagar, Hudson Circle, Bangalore - 560027 <br>
                                           PH (080 22292470 / 71).
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <img class="logo-check" src="{{URL::to('/')}}/images/logo.png" />
                                    </td>
                                </tr>


                            </table>



                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <style>
        * {
          -webkit-print-color-adjust: exact;   /* Chrome, Safari */
          color-adjust: exact;                 /*Firefox*/
        }
        #printArea {
            /* display: none; */
        }

        img.transform {
            max-width: 100px;
        }
        @media print {
            .no-print {
                display: none;
            }
            /*body * {*/
            /*    visibility: hidden;*/
            /*}*/
            #printArea * {
                visibility: visible;
            }

            #printArea {
                display: block;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            @page{
                size: A4;
                margin: 2mm;
                text-align: center;
                orphans: 0!important;
                widows: 0!important;
            }
        }
    </style>
@endsection
