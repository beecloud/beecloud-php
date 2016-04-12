<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 9/29/15
 * Time: 16:05
 */

require_once("../../loader.php");
date_default_timezone_set("Asia/Shanghai");

$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["bill_no"] = $_POST["billNo"];
//选填 channel
$data["channel"] = "WX_NATIVE";

$result = $api->bills($data);

print json_encode($result);






