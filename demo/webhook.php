<?php
/**
 * http类型为 Application/json, 非XMLHttpRequest的application/x-www-form-urlencoded, $_POST方式是不能获取到的，
 * APP ID和Master Secret可以在https://beecloud.cn平台登录后获取
 *
 * 备注：secret是一个非常重要的数据，请您必须小心谨慎的确保此数据保存在足够安全的地方。
 *      您从BeeCloud官方获得此数据的同时，即表明您保证不会被用于非法用途和不会在没有得到您授权的情况下被盗用，
 *      一旦因此数据保管不善而导致的经济损失及法律责任，均由您独自承担。
 */
$appId = "";
$masterSecret = "";
$jsonStr = file_get_contents("php://input");

$msg = json_decode($jsonStr);

// webhook字段文档: https://beecloud.cn/doc/#3-webhook或者https://github.com/beecloud/beecloud-webhook

//第一步:验证签名
//原签名算法：app_id + app_secret ＋ timestamp
//$sign = md5($appId . $appSecret . $msg->timestamp);
//if ($sign != $msg->sign) {
//    // 签名不正确
//    exit();
//}
//现修正如下：app_id + transaction_id + transaction_type + channel_type + transaction_fee + master_secret
$sign = md5($appId . $msg->transaction_id . $msg->transaction_type . $msg->channel_type . $msg->transaction_fee.$masterSecret);
if ($sign != $msg->signature) {
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
 * 推送企业打款结果的
 * transaction_id就是企业打款的交易单号, 对应支付请求的bill_no, transaction_type为TRANSFER, sub_channel_type为BC_TRANSFER
 * message_detail中包含打款相关的详细信息
 *
 */
if ($msg->transaction_type == "TRANSFER") {
    //企业打款状态是否为成功,true代表成功
    $status = $msg->trade_success;

    //message_detail 参考文档
    switch ($msg->channel_type) {
        case "BC":
            if($msg->sub_channel_type == 'BC_TRANSFER'){
                //message_detail中包含打款相关的详细信息
                //TODO...
            }
            break;
    }
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

//打印所有字段,仅供调试使用
print_r($msg);

//处理消息成功,不需要持续通知此消息返回success
echo 'success';
