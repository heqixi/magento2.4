<?php
$userData = array("username" => "admin", "password" => "admin123");
$ch = curl_init("http://107.182.30.71/rest/V1/integration/admin/token");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Length: " . strlen(json_encode($userData, true))));

$token = curl_exec($ch);
$token = json_decode($token);
print_r($token);
curl_close($ch);
