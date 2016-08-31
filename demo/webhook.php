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
/*
 * 推送支付的结果
 */
if($msg->transaction_type == "PAY") {
    //付款信息
    //支付状态是否变为支付成功,true代表成功
    $status = $msg->trade_success;

    //message_detail 参考文档
    //channel_type 微信/支付宝/银联/快钱/京东/百度/易宝/PAYPAL/BC
    switch ($msg->channel_type) {
        case "WX":
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
        case "BC":
            //BC订阅收费
            if($msg->sub_channel_type == 'BC_SUBSCRIPTION'){

            }
            //BC代扣
            if($msg->sub_channel_type == 'BC_CARD_CHARGE'){

            }
            break;
    }
} else if ($msg->transaction_type == "REFUND") { //退款的结果

}

/*
 * 推送订阅结果的
 * transaction_id就是创建订阅时返回的订阅id，transaction_type为SUBSCRIPTION，sub_channel_type为BC_SUBSCRIPTION，
 * message_detail中包含用户相关的注册信息，其中的card_id注意留存。
 * 该id由{bank_name、card_no、id_name、id_no、mobile}共同决定，可以直接用于发起订阅
 */
if ($msg->transaction_type == "SUBSCRIPTION") {
    //创建的订阅状态是否为成功,true代表成功
    $status = $msg->trade_success;

    //message_detail 参考文档
    switch ($msg->channel_type) {
        case "BC":
            if($msg->sub_channel_type == 'BC_SUBSCRIPTION'){
                //message_detail中包含签约相关的详细信息，包括card_id
                //TODO...
            }
            break;
    }
}

/*
 * 推送代扣签约结果的
 * transaction_id就是代扣签约返回的id，transaction_type为SIGN，sub_channel_type为BC_CARD_CHARGE，
 * message_detail中包含签约相关的详细信息，其中的card_id注意留存。
 */
if ($msg->transaction_type == "SIGN") {
    //创建的代扣签约状态是否为成功,true代表成功
    $status = $msg->trade_success;

    //message_detail 参考文档
    switch ($msg->channel_type) {
        case "BC":
            if($msg->sub_channel_type == 'BC_CARD_CHARGE'){
                //message_detail中包含签约相关的详细信息，包括card_id
                //TODO...
            }
            break;
    }
}

//打印所有字段
print_r($msg);

//处理消息成功,不需要持续通知此消息返回success
echo 'success';
