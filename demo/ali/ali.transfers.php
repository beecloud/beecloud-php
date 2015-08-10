<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud支付宝批量打款示例</title>
</head>
<body>
<?php
require_once("../../sdk/beecloud.php");

$data = array();
$appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
$data["app_id"] = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "ALI";
$data["batch_no"] = "bcdemo" . $data["timestamp"];
$data["account_name"] = "白开水";
$data["transfer_data"] = array();
$data["transfer_data"][] = json_decode(json_encode(array(
        "transfer_id" => "8000",
        "receiver_account" => "test",
        "receiver_name" =>"tst",
        "transfer_fee" => 1,
        "transfer_note" => ""
    )));
//
//transfer_id	String	付款流水号，32位以内数字字母	1507290001
//receiver_account	String	收款方支付宝账号	someone@126.com
//receiver_name	String	收款方支付宝账户名	某某人
//transfer_fee	int	付款金额，单位为分	100
//transfer_note	String	付款备注	打赏

//$data["return_url"] = "http://payservice.beecloud.cn";

//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));
//选填 show_url
//$data["show_url"] = "";

try {
    $result = BCRESTApi::bill($data);
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }

    $htmlContent = $result->html;
    $url = $result->url;
    echo $htmlContent;
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</body>
</html>