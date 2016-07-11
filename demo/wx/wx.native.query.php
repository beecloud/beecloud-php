<?php
require_once("../../loader.php");
require_once("../config.php");

//设置app id, app secret, master secret, test secret
$api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);

$data["timestamp"] = time() * 1000;
$data["bill_no"] = $_GET["billNo"];
//选填 channel
$data["channel"] = "WX_NATIVE";

$result = $api->bills($data);
print json_encode($result);
