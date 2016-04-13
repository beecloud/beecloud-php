<?php
//余额修改
require_once("../../loader.php");
$data = array(
    'email' => '',
    'delta_amount' => ''
);

$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);

try {
    $result = $api->gateway_amount($data, 'put');
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }
    echo '修改成功';
} catch (Exception $e) {
    echo $e->getMessage();
}