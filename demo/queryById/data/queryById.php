<?php
require_once("../../../loader.php");
date_default_timezone_set("Asia/Shanghai");

$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["id"] = $_GET['id'];
$type = $_GET['type'];

if(empty($data["id"])){
    exit(json_encode(array('result_code' => 1, 'err_detail' => '请输入id')));
}
try {
    switch($type){
        case 'bill':
            $result = $api->bill($data, 'get');
            if ($result->result_code != 0 || $result->result_msg != "OK") {
                echo json_encode(array('result_code' => 1, 'err_detail' => $result->err_detail));
                exit();
            }
            $result->pay->create_time = isset($result->pay->create_time) && $result->pay->create_time ? date('Y-m-d H:i:s', $result->pay->create_time/1000) : '';
            echo json_encode(array('result_code' => 0, 'data' => $result->pay));
            break;
        case 'refund':
            $result = $api->refund($data, 'get');
            if ($result->result_code != 0 || $result->result_msg != "OK") {
                echo json_encode(array('result_code' => 1, 'err_detail' => $result->err_detail));
                exit();
            }
            $result->refund->create_time = isset($result->refund->create_time) && $result->refund->create_time ? date('Y-m-d H:i:s', $result->refund->create_time/1000) : '';
            echo json_encode(array('result_code' => 0, 'data' => $result->refund));
            break;
    }
} catch (Exception $e) {
    die($e->getMessage());
}
