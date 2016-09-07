<?php
require_once("../../loader.php");
require_once("../config.php");

//设置app id, app secret, master secret, test secret
$api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
$data["timestamp"] = time() * 1000;
$data["channel"] = $_GET['channel'];
$data["bill_no"] = $_GET["billNo"];
try {
    switch ($data['channel']){
        case 'BC_ALI_QRCODE':
            $result = $api->bills($data);
            if ($result->result_code != 0) {
                print_r($result);
                exit();
            }
            if (isset($result->bills[0]) && $result->bills[0]){
                echo json_encode(array('resultCode' => 0, 'pay_result' => $result->bills[0]->spay_result));
            }else{
                print_r($result);
                exit();
            }
            break;
        case 'ALI_OFFLINE_QRCODE':
            $result = $api->offline_bill_status($data);
            if ($result->result_code != 0) {
                print_r($result);
                exit();
            }
            echo json_encode($result);
            break;
        default:
            exit('channel must be BC_ALI_QRCODE  or ALI_OFFLINE_QRCODE');
            break;
    }
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}