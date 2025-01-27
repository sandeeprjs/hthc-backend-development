<?php
// Account details
$apiKey = urlencode('629b1a5d3a04dee3363302cbbe4731a57fcc2d90be0c804fc4c750a412588b3c');

$data = array('apikey' => $apiKey);

// Send the POST request with cURL
$ch = curl_init('https://api.textlocal.in/get_templates/');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Process your response here
echo $response;