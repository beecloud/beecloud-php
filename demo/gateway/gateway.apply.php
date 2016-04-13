<?php
//提现申请
require_once("../../loader.php");

/*
 *  bank_name 参数列表
    CMB	    招商银行
    ICBC	工商银行
    CCB	    建设银行（暂时不支持）
    BOC	    中国银行
    ABC	    农业银行
    BOCM	交通银行
    SPDB	浦发银行
    GDB	    广发银行
    CITIC	中信银行
    CEB	    光大银行
    CIB	    兴业银行
    SDB	    平安银行
    CMBC	民生银行
 *
 */
$data = array(
    'bank_account_name' => '',
    'bank_account_no' => '',
    'bank_name' => '',
    'branch_bank_name' => '',
    'subbranch_bank_name' => '',
    'is_personal' => '',
    'bank_province' => '',
    'bank_city', 'note' => '',
    'email' => '',
    'withdraw_amount' => ''
);

$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);


try {
    $result = $api->gateway_withdraw($data, 'post');
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }
    echo  '提现纪录id: '.$result->id;
} catch (Exception $e) {
    echo $e->getMessage();
}