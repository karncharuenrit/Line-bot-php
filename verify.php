<?php
$access_token = 'XKj2ZnsCmYOyRuZwgCCkP4J37Dd+D97qv4aajOWUkTeh9erjOmYC4hXSwiI1mJTuaQ0Z0B2ZOQHHW4jMYWifptIb29Gew62KWD/8oMSN+eGPcgBuVz3f76z+kQIzAqu5BtSJ/gRnNuZUu3IuT2CqowdB04t89/1O/w1cDnyilFU=';

$url = 'https://api.line.me/v1/oauth/verify';

$headers = array('Authorization: Bearer ' . $access_token);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

echo $result;
