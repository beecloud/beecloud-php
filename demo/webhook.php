<?php
/**
 * http类型为 Application/json, 非XMLHttpRequest的application/x-www-form-urlencoded, $_POST方式是不能获取到的
 */
$appId = "";
$appSecret = "";
$jsonStr = file_get_contents("php://input");

$msg = json_decode($jsonStr);

// webhook字段文档: https://beecloud.cn/doc/?index=webhook

//第一步:验证签名
$sign = md5($appId . $appSecret . $msg->timestamp);
if ($sign != $msg->sign) {
    // 签名不正确
    exit();
}


//第二步:过滤重复的Webhook
//客户需要根据订单号进行判重，忽略已经处理过的订单号对应的Webhook
//if(transaction_id对应的订单号已经处理完毕){
//  exit();
//}
//

//第三步:验证订单金额与购买的产品实际金额是否一致
//也就是验证调用Webhook返回的transaction_fee订单金额是否与客户服务端内部的数据库查询得到对应的产品的金额是否相同
//if($msg->transaction_fee != 客户服务端查询得到的实际产品金额){
//  exit();
//}

//第四步:处理业务逻辑和返回
if($msg->transaction_type == "PAY") { //支付的结果
    //付款信息
    //支付状态是否变为支付成功,true代表成功
    $result = $msg->trade_success;

    //message_detail 参考文档
    //channel_type 微信/支付宝/银联/快钱/京东/百度/易宝/PAYPAL
    switch ($msg->channel_type) {
        case "WX":
            /**
             * 处理业务
             */
            break;
        case "ALI":
            break;
        case "UN":
            break;
        case "KUAIQIAN":
            break;
        case "JD":
            break;
        case "BD":
            break;
        case "YEE":
            break;
        case "PAYPAL":
            break;
    }
} else if ($msg->transaction_type == "REFUND") { //退款的结果

}

//打印所有字段
print_r($msg);

//处理消息成功,不需要持续通知此消息返回success
echo 'success';
