<?php
require_once("../loader.php");
require_once("config.php");

try {
    /* registerApp fun need four params:
     * @param(first) $app_id beecloud平台的APP ID
     * @param(second) $app_secret  beecloud平台的APP SECRET
     * @param(third) $master_secret  beecloud平台的MASTER SECRET
     * @param(fouth) $test_secret  beecloud平台的TEST SECRET, for sandbox
     */
    $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
    //不支持测试模式
    $api->setSandbox(false);

    //\beecloud\rest\api::registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
    //\beecloud\rest\api::setSandbox(false);
}catch(Exception $e){
    die($e->getMessage());
}

$data = array();
$data["timestamp"] = time() * 1000;
//total_fee(int 类型) 单位分,  最小金额150分
$data["total_fee"] = 1;
$data["bill_no"] = 'phpdemo' . $data['timestamp'];
//title UTF8编码格式，32个字节内，最长支持16个汉字
$data["title"] = 'PHP认证支付测试';
//支付渠道
$data["channel"] = 'BC_EXPRESS';
//用于标识一个用户，id必须唯一
$data["buyer_id"] = 'beecloud';
//第一次发起支付时，在optional中传入phone_no(手机号)，card_no（银行卡号），id_no（身份证号），customer_name（银行卡持有者姓名）等四个要素，
//第一次发起支付成功后，可以传入token（第一次发起支付时返回的授权码）一个要素即可
$data["optional"] = (object)array('id_no' => '21302619870917xxxx', 'customer_name' => 'xxxx', 'card_no' => '622622180408xxxx', 'phone_no' => '1596214xxxx');
//$data["optional"] = (object)array('token' => '235BAFEF6039440C7045D1A5E972xxxx');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud <?php echo $data["title"];?></title>
</head>
<body>
<?php
try {
    //第一步: 获取验证码；得到参数token, 支付记录的唯一标识id以及手机上收到的短信验证码
    $result =  $api->bill($data);
    if ($result->result_code != 0) {
        print_r($result);
        exit();
    }
    //第二步: 确认支付；传入token、支付记录的id、短信验证码
    $verify = array(
        'bc_bill_id' => $result->id, //BeeCloud生成的唯一支付记录id
        'token' => $result->token,  //渠道返回的token
        'verify_code' => 'xxxxxx'   //短信验证码
    );
    $result = $api->confirm_bill_pay($verify);
    if ($result->result_code != 0) {
        print_r($result);
        exit();
    }
    echo '支付成功';
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</body>
</html>