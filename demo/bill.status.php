<?php
require_once("../loader.php");
require_once("config.php");

try {
    //设置app id, app secret, master secret, test secret
    $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
    $data["timestamp"] = time() * 1000;
    $data["channel"] = $_GET['channel'];
    $data["bill_no"] = $_GET["billNo"];

    switch ($data['channel']){
        case 'BC_ALI_QRCODE':
            $result = $api->bills($data);
            if ($result->result_code != 0) {
                echo json_encode($result);
                exit();
            }
            echo json_encode(array('result_code' => 0, 'pay_result' => isset($result->bills[0]) && $result->bills[0] ? $result->bills[0]->spay_result : false));
            break;
        case 'BC_QQ_NATIVE':
        case 'BC_JD_QRCODE':
        case 'BC_NATIVE':
            $result = $api->bills($data);
            if ($result->result_code != 0) {
                echo json_encode($result);
                exit();
            }
            $pay_result = false;
            foreach ($result->bills as $bill){
                if($bill->spay_result){
                    $pay_result = true;
                    break;
                }
            }
            echo json_encode(array('result_code' => 0, 'pay_result' => $pay_result));
            break;
        case 'ALI_OFFLINE_QRCODE':
        case 'ALI_SCAN':
        case 'WX_SCAN':
        case 'WX_NATIVE':
            $result = $api->offline_bill_status($data);
            if ($result->result_code != 0) {
                echo json_encode($result);
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