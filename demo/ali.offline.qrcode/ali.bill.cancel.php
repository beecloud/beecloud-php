<?php
require_once("../../loader.php");

$data = array();
$appSecret = APP_SECRET;
$data["app_id"] = APP_ID;
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "ALI_OFFLINE_QRCODE";
$data["bill_no"] = $_GET["billNo"];
$data["method"] = "REVERT";


try {
    $result = $api->offline_bill($data);
    if ($result->result_code != 0) {
        print_r($result);
        exit();
    }
    echo json_encode($result);
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}