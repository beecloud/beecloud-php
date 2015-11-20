
<?php
require_once("../../../beecloud.php");

$type = $_GET["type"];
$amount = $_GET["amount"];
$userid = $_GET["userid"];
$username = $_GET["username"];
$openid = $_GET["openid"];



$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["total_fee"] = (int) $amount;
$data["desc"] = "test";

if ($type == "ali") {
    $data["channel"] = "ALI_TRANSFER";
    $data["transfer_no"] = "trans" . time();

    /**
     * 收款方的id 账号和 名字也需要对应
     */
    $data["channel_user_id"] = $userid;
    $data["channel_user_name"] = $username;

    $data["account_name"] = "苏州比可网络科技有限公司"; //注意此处需要和企业账号对应的全称
} else {
    $data["transfer_no"] = "".time();//微信要求10位数字
    $data["channel"] = "WX_TRANSFER";
    $data["channel_user_id"] = $openid;
    if ($type == "wxred") {
        $data["channel"] = "WX_REDPACK";
        $data["redpack_info"] = json_decode(
            json_encode(array(
                "send_name" => "BeeCloud",
                "wishing" => "test",
                "act_name" => "testA"
            ))
        );
    }
}



try {
    $result = BCRESTApi::transfer($data);
    $return = array("resultCode"=>1);
    if ($result->result_code != 0) {
        $return["errMsg"] = $result;
        $return["data"] = $data;
        echo json_encode($return);
        exit();
    }

    $return["resultCode"] = 0;
    $return["url"] = $result->url;

    echo json_encode($return);
} catch (Exception $e) {
    echo $e->getMessage();
}
