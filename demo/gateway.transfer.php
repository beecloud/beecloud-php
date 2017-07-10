<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeePay自动打款-打款到银行卡示例</title>
</head>
<body>
<?php
    try {
        require_once("../loader.php");
        require_once("config.php");

        //$data["app_id"] = APP_ID; //必须是正整数，单位为分
        $data["withdraw_amount"] = 1; //必须是正整数，单位为分
        //商户订单号, 8到32位数字和/或字母组合，请自行确保在商户系统中唯一，同一订单号不可重复提交，否则会造成订单重复
        $data["bill_no"] = "phptransfer" . $data["timestamp"];

        $data["transfer_type"] = "1"; //"1"代表对私打款，"2"代表对公打款
        $data["bank_name"] = "中国银行"; //银行全称, 不能写银行的缩写
        $data["bank_account_no"] = "622269192199384xxxx"; //收款方的银行卡号
        $data["bank_account_name"] = "刘"; //收款方的姓名或者单位名
        $data["bank_code"] = "BOC"; //银行的标准编码
        $data["note"] = "测试";   //用户付款原因
        //选填optional
        $data["optional"] = (object)array("tag"=>"msgtoreturn"); //附加数据
        //选填notify_url，商户可通过此参数设定回调地址，此地址会覆盖用户在控制台设置的回调地址。必须以http://或https://开头
        //$data["notify_url"] = "";

        /*
         * 对关键参数的签名，签名方式为MD5（32位小写字符）, 编码格式为UTF-8
         * 验签规则即：app_id + bill_no + withdraw_amount + bank_account_no + master_secret的MD5生成的签名
         * 其中master_secret为用户创建Beecloud App时获取的参数。
         */
        //$data["signature"] = md5($data["app_id"] . $data["bill_no"] . $data["withdraw_amount"] . $data["bank_account_no"] . MASTER_SECRET);
        //设置app id, app secret, master secret, test secret
        $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
        $result = $api->gateway_transfer($data);
        /*
         *  返回结果:json格式，错误码（错误详细信息 参考err_detail字段)，如下所示：
         *  result_code result_msg      含义
         *   0	        OK	            调用成功
         *   1	        APP_INVALID	    根据app_id找不到对应的APP或者app_sign不正确
         *   4	        MISS_PARAM	    缺少必填参数
         *   5	        PARAM_INVALID	参数不合法
         *   14	        RUNTIME_ERROR	运行时错误
         *   15	        NETWORK_ERROR	网络异常错误
         */
        if ($result->result_code != 0) {
            print_r($result);
            exit();
        }
        echo '打款成功, 打款记录唯一标识: ' . $result->id;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>
</body>
</table>
</body>
</html>