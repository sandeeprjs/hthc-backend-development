<html>
<body>
@if($mailto == 'sender')
<h1>Hello {{ $booking->customer_name}}, </h1> <br>
@endif
@if($mailto == 'receiver')
<h1>Hello {{ $delivery->receiver_name}}, </h1> <br>
@endif
    Your consignment {{ $booking->consg_number }} has been booked. Please click to <a href="{{ url('/track?consg_number='.$booking->consg_number) }}" > track </a>
     <br>
     @if($mailto == 'sender')
      & Shipper copy to, <a href="{{ url('/booking/acknowledgement/s-'.$booking->consg_number) }}" > Click here</a>
     @endif
     <br>&nbsp;<br>
     Regards,
     <br>
     <b>HTHC</b>
    
</body>

</html>