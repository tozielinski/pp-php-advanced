<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once ('config/Config.php');
include_once ('helper/PayPalHelper.php');

$paypalHelper = new PayPalHelper;

header('Content-Type: application/json');
echo json_encode($paypalHelper->createOrder($_POST['payload']));
?>