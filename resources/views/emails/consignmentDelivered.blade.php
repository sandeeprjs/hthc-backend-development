<html>
<body>
@if($mailto == 'sender')
<h1>Hello {{ $booking->customer_name}}, </h1> <br>
@endif
@if($mailto == 'receiver')
<h1>Hello {{ $delivery->receiver_name}}, </h1> <br>
@endif
     Your consignment {{ $booking->consg_number }} has been successfully delivered. 
     <br>&nbsp;<br>
     Regards,
     <br>
     <b>HTHC</b>
    
</body>

</html>