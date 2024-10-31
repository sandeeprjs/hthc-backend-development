<html>
<body>

<h1>Hello {{ $customerName }}, </h1> <br>
   Your consignments are booked. Thanks for using Hand to Hand Courier Service. Please click to <a href={{ url('/booking/'.$batchId) }} > track </a>
   & Shipper Copy <a href={{ url('/booking/acknowledgement/s-'.$batchId) }} > Click Here </a>
   <br>&nbsp;<br><br>Regards, <br> <b> HTHC </b>

</body>

</html>