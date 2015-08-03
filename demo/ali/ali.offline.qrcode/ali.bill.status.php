<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 8/3/15
 * Time: 15:22
 */
require_once("../../../sdk/beecloud.php");
$billNo = $_GET["billNo"];
$data = array();
$appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
$data["app_id"] = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "ALI_OFFLINE_QRCODE";
$data["bill_no"] = $billNo;
$data["method"] = "UPDATE";


try {
    $result = BCRESTApi::billStatus($data);
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }
    echo json_encode($result);
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}