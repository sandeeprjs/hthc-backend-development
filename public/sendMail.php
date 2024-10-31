<?php
$to = "janagiraman@netiapps.com";
$subject = "My subject";
$txt = "Hello world!";
$headers = "From: janagiramm@gmail.com";

if(mail($to,$subject,$txt,$headers)){
    echo 'success';
}else{
    echo 'failed';
}


?>