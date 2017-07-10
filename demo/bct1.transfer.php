<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeePay代付示例</title>
</head>
<body>
<?php
    try {
        require_once("../loader.php");
        require_once("config.php");


        //设置app id, app secret, master secret, test secret
        $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);

        //$data["app_id"] = APP_ID; //必须是正整数，单位为分
        $data["total_fee"] = 1; //必须是正整数，单位为分
        //商户订单号, 8到32位数字和/或字母组合，请自行确保在商户系统中唯一，同一订单号不可重复提交，否则会造成订单重复
        $data["bill_no"] = 'phptransfer' . time();
        $data["is_personal"] = '0'; //"1"代表对私打款，"0"代表对公打款
        $data["bank_account_no"] = '622269192199384xxxx'; //收款方的银行卡号
        $data["bank_account_name"] = '刘xx'; //收款方的姓名或者单位名
        /*
         * 对关键参数的签名，签名方式为MD5（32位小写字符）, 编码格式为UTF-8
         * 验签规则即：app_id + bill_no + total_fee + bank_account_no + master_secret的MD5生成的签名
         * 其中master_secret为用户创建Beecloud App时获取的参数。
         */
        //$data["signature"] = md5($data["app_id"] . $data["bill_no"] . $data["total_fee"] . $data["bank_account_no"] . MASTER_SECRET);

        //获取银行列表
        /*$banks = $api->get_banks($data, 'T1_EXPRESS_TRANSFER');
        if ($result->result_code != 0) {
            print_r($result);
            exit();
        }
        print_r($result->banks);*/
        $data["bank_name"] = "中国工商银行"; //银行全称, 不能写银行的缩写
        //选填optional
        //$data["optional"] = (object)array("key"=>"value"); //附加数据

        $result = $api->bct1_transfer($data);
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
        echo '打款成功';
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>
</body>
</table>
</body>
</html>