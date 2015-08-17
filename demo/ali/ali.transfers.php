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
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "ALI";
$data["batch_no"] = "bcdemo" . $data["timestamp"];
$data["account_name"] = "苏州比可网络科技有限公司";
$data["transfer_data"] = array();
$data["transfer_data"][] = json_decode(json_encode(array(
        "transfer_id" => "bf693b3121864f3f969a3e1ebc5c376a",
        "receiver_account" => "baoee753@163.com",
        "receiver_name" =>"钱志浩",
        "transfer_fee" => 100,
        "transfer_note" => "test"
    )));
$data["transfer_data"][] = json_decode(json_encode(array(
    "transfer_id" => "bf693b3121864f3f969a3e1ebc5c3768",
    "receiver_account" => "baoee753@163.com",
    "receiver_name" =>"钱志浩",
    "transfer_fee" => 100,
    "transfer_note" => "test"
)));

//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));

try {
    $result = BCRESTApi::transfers($data);
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }
    $url = $result->url;
    ?>
    <a href="<?php echo $url;?>">点击开始批量打款</a>
    <?php
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</body>
</html>