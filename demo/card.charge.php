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
$data["total_fee"] = 160;
$data["bill_no"] = "phpdemo" . $data["timestamp"];
//title UTF8编码格式，32个字节内，最长支持16个汉字
$data["title"] = 'PHP '.$_GET['type'].'支付测试';
//渠道类型:ALI_WEB 或 ALI_QRCODE 或 UN_WEB或JD_WAP或JD_WEB时为必填
$data["return_url"] = "https://beecloud.cn";
//选填 optional
$data["optional"] = (object)array("tag"=>"msgtoreturn");
//选填 订单失效时间bill_timeout
//必须为非零正整数，单位为秒，建议最短失效时间间隔必须大于360秒
//京东(JD*)不支持该参数。
//$data["bill_timeout"] = 360;

$type = $_GET['type'];
switch($type){
    case 'BC_CARD_CHARGE' :
        $data["channel"] = "BC_CARD_CHARGE";
        $title = "BC代扣";
        break;
    default :
        exit("No this type.");
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud<?php echo $title;?>支付示例</title>
</head>
<body>
<?php
    try {
        //第一步: 获取验证码,得到参数sms_id, sms_code: 请查看手机收到的验证码
        $sms = array(
            'phone' => '159621xxxxx',
            'timestamp' => $data['timestamp']
        );
        $result = $api->sms($sms);
        if ($result->result_code != 0) {
            print_r($result);
            exit();
        }
        //echo 'sms_id: '.$result->sms_id;

        /*
         * 第二步: 签约API, 配置webhook,签约成功之后, 获取到card_id(注意保存)
         * 具体参数含义如下:
         *   mobile 手机号
         *   bank  银行名称
         *   id_no 身份证号
         *   name   姓名
         *   card_no 银行卡号(借记卡,不支持信用卡)
         *   sms_id  获取验证码接口返回验证码记录的唯一标识
         *   sms_code 手机端接收到验证码
         */
        $sign = array(
            'timestamp' => $data['timestamp'],
            'mobile' => '1596214xxxxx',
            'bank' => '中国银行',
            'id_no' => '413026xxxxxxxxxxx',
            'name' => 'jason',
            'card_no' => '6226xxxxxxxxxxxxxxxx',
            'sms_id' => 'd4fb7cdd-13ff-4c6c-ac57-df5aee717988',
            'sms_code' => '374932',
        );
        $result = $api->card_charge_sign($sign);
        if ($result->result_code != 0) {
            print_r($result);
            exit();
        }
        //echo '签约成功';
        //第三步: 通过第二步取到card_id,进行支付
        $data['card_id'] = '21e58f1c-de22-4979-a95c-3dfxxxxxx';
        $result =  $api->bill($data);
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
