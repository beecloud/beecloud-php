
<?php
require_once("../../../beecloud.php");

$cardInfo = json_decode($_POST["cardinfo"]);
$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "PAYPAL_CREDITCARD";
$data["total_fee"] = 1;
$data["bill_no"] = "bcdemo" . $data["timestamp"];
$data["title"] = "test";
$data["currency"] = "USD";
$data["credit_card_info"] = $cardInfo;

//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));
//选填 show_url
//$data["show_url"] = "";

try {
    $result = BCRESTInternational::bill($data);
    $return = array("resultCode"=>1);
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }

    $return["resultCode"] = 0;
    $return["url"] = $result->url;
    $return["result"] = $result;
    echo json_encode($return);
} catch (Exception $e) {
    echo $e->getMessage();
}