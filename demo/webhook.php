<?php
/**
 * http类型为 Application/json, 非XMLHttpRequest的application/x-www-form-urlencoded, $_POST方式是不能获取到的
 */
$appId = "";
$appSecret = "";
$jsonStr = file_get_contents("php://input");

$msg = json_decode($jsonStr);

// webhook字段文档: http://beecloud.cn/doc/php.php#webhook

// 验证签名
$sign = md5($appId . $appSecret . $msg->timestamp);

if ($sign != $msg->sign) {
    // 签名不正确
    exit();
}

if($msg->transactionType == "PAY") {
    //付款信息
    //支付状态是否变为支付成功
    $result = $msg->tradeSuccess;

    //messageDetail 参考文档
    switch ($msg->channelType) {
        case "WX":
            /**
             * 处理业务
             */
            break;
        case "ALI":
            break;
        case "UN":
            break;
    }
} else if ($msg->transactionType == "PAY") {

}

//打印所有字段
print_r($msg);

//处理消息成功,不需要持续通知此消息返回success
echo 'success';
