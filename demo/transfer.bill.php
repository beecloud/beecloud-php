<?php
require_once("../loader.php");

$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["total_fee"] = 1;
$data["desc"] = "transfer test";

$type = $_GET['type'];
switch($type) {
    case 'WX_REDPACK' :
        $title = '微信红包';
        $data["transfer_no"] = "".time();//微信要求10位数字
        $data["total_fee"] = 100;  //单个微信红包金额介于[1.00元，200.00元]之间
        $data["channel_user_id"] = '';  //微信用户openid
        $data["channel"] = "WX_REDPACK";
        $data["redpack_info"] = (object)array(
            "send_name" => "BeeCloud",
            "wishing" => "test",
            "act_name" => "testA"
        );
        break;
    case 'WX_TRANSFER' :
        $title = '微信企业打款';
        $data["transfer_no"] = "".time();//微信要求10位数字
        $data["channel"] = "WX_TRANSFER";
        $data["channel_user_id"] = '';   //微信用户openid
        break;
    case 'ALI_TRANSFER' :
        $title = '支付宝企业打款';
        $data["channel"] = "ALI_TRANSFER";
        $data["transfer_no"] = "trans" . time();

        //收款方的id 账号和 名字也需要对应
        $data["channel_user_id"] = '';   //收款人账户
        $data["channel_user_name"] = ''; //收款人账户姓名

        $data["account_name"] = "苏州比可网络科技有限公司"; //注意此处需要和企业账号对应的全称
        break;
    case 'ALI_TRANSFERS' :
        $title = '支付宝批量打款';
        $data["channel"] = "ALI";
        $data["batch_no"] = "bcdemo" . $data["timestamp"];
        $data["account_name"] = "苏州比可网络科技有限公司";
        $data["transfer_data"] = array(
            (object)array(
                "transfer_id" => "bf693b3121864f3f969a3e1ebc5c376a",
                "receiver_account" => "", //收款方账户
                "receiver_name" =>"",     //收款方账号姓名
                "transfer_fee" => 1,      //打款金额，单位为分
                "transfer_note" => "test"
            ),
            (object)array(
                "transfer_id" => "bf693b3121864f3f969a3e1ebc5c3768",
                "receiver_account" => "",
                "receiver_name" =>"",
                "transfer_fee" => 1,
                "transfer_note" => "test"
            )
        );
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud<?php echo $title; ?>示例</title>
</head>
<body>
<?php
    try {
        if($type == "ALI_TRANSFERS"){
            $result = $api->transfers($data);
        }else{
            $result = $api->transfer($data);
        }
        if ($result->result_code != 0) {
            echo json_encode($result);
            exit();
        }
        if(isset($result->url)){
            header("Location:$result->url");
        }else{
            echo '下发成功!';
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>
</body>
</table>
</body>
</html>