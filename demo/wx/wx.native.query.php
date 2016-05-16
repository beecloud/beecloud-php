<?php
require_once("../../loader.php");

$data = array();
$appSecret = APP_SECRET;
$data["app_id"] = APP_ID;
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["bill_no"] = $_GET["billNo"];
//选填 channel
$data["channel"] = "WX_NATIVE";

$result = $api->bills($data);
print json_encode($result);

