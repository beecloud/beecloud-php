<?php
require_once("../../loader.php");

$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "ALI_OFFLINE_QRCODE";
$data["bill_no"] = $_GET["billNo"];
try {
    $result = $api->offline_bill_status($data);
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }
    echo json_encode($result);
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}