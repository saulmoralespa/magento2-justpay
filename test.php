<?php

/**
[form_key] => xbtBnjOgMOmW6nXX
[public_key] => KJSAKKKASD812312lKKAS
[time] => 2019-08-24T13:37:37
[channel] => 1
[amount] => 1005.00
[currency] => CLP
[trans_ID] => 34_1566653387
[PaymentCode] => 606025
[signature] => 19e96a22402a782a54395294d45c784b4deaf34d0e32ea906cc69c744702dbf2
 */


$public_key = 'KJSAKKKASD812312lKKAS';
$time = '2019-08-24T13:37:37';
$channel = 1;
$amount = 1005.00;
$currency = 'CLP';
$trans_id = '34_1566653387';
$secure_key = 'UYSDJH56122131ADSD11';

$data_sign = "$time$channel$amount$currency$trans_id$secure_key";
$signature_create = hash('sha256', $data_sign);

die($signature_create);
