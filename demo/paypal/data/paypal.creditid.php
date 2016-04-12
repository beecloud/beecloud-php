
<?php
require_once("../../../loader.php");

$cardId = $_POST["cardId"];
$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "PAYPAL_SAVED_CREDITCARD";
$data["total_fee"] = 1;
$data["bill_no"] = "bcdemo" . $data["timestamp"];
$data["title"] = "test";
$data["currency"] = "USD";
$data["credit_card_id"] = $cardId;

//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));

try {
    $result = $international->bill($data);
    $return = array("resultCode"=>1);
    if ($result->result_code != 0) {
        echo json_encode($return);
        exit();
    }

    $return["resultCode"] = 0;
    $return["url"] = $result->url;

    echo json_encode($return);
} catch (Exception $e) {
    echo $e->getMessage();
}