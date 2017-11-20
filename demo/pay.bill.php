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
    //Test Model,只提供下单和支付订单查询的Sandbox模式,不写setSandbox函数或者false即live模式,true即test模式
    $api->setSandbox(false);

    //\beecloud\rest\api::registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
    //\beecloud\rest\api::setSandbox(false);
}catch(Exception $e){
    die($e->getMessage());
}

$data = array();
$data["timestamp"] = time() * 1000;
//total_fee(int 类型) 单位分
$data["total_fee"] = 1;
$data["bill_no"] = "phpdemo" . $data["timestamp"];
//title UTF8编码格式，32个字节内，最长支持16个汉字
$data["title"] = 'PHP '.$_GET['type'].'支付测试';
//渠道类型:ALI_WEB 或 ALI_QRCODE 或 UN_WEB或JD_WAP或JD_WEB, BC_GATEWAY为京东、BC_WX_WAP、BC_ALI_WEB渠道时为必填, BC_ALI_WAP不支持此参数
$data["return_url"] = "https://beecloud.cn";
//选填 optional, 附加数据, eg: {"key1”:“value1”,“key2”:“value2”}
//用户自定义的参数，将会在webhook通知中原样返回，该字段主要用于商户携带订单的自定义数据
$data["optional"] = (object)array("key"=>"value");
//选填 订单失效时间bill_timeout
//必须为非零正整数，单位为秒，建议最短失效时间间隔必须大于360秒
//京东(JD*)不支持该参数。
//$data["bill_timeout"] = 360;

/**
 * notify_url 选填，该参数是为接收支付之后返回信息的,仅适用于线上支付方式, 等同于在beecloud平台配置webhook，
 * 如果两者都设置了，则优先使用notify_url。配置时请结合自己的项目谨慎配置，具体请
 * 参考demo/webhook.php
 *
 */
//$data['notify_url'] = 'https://beecloud.cn';

/**
 * buyer_id选填
 * 商户为其用户分配的ID.可以是email、手机号、随机字符串等；最长64位；在商户自己系统内必须保证唯一。
 */
//$data['buyer_id'] = 'xxxx';


/*
 * coupon_id string 选填 卡券id
 * 传入卡券id，下单时会自动扣除优惠金额再发起支付
 *
 */
//$data['coupon_id'] = 'xxxxxx';

/**
 * analysis选填, 分析数据
 * 用于统计分析的数据，将会在控制台的统计分析报表中展示，用户自愿上传。包括以下基本字段：
 *      os_name(系统名称，如"iOS"，"Android") os_version(系统版本，如"5.1") model(手机型号，如"iPhone 6")
 *      app_name(应用名称) app_version(应用版本号) device_id(设备ID) category(类别，用户可自定义，如游戏分发渠道，门店ID等)
 *      browser_name(浏览器名称) browser_version(浏览器版本)
 * 下单产品保存格式：
 *      product固定的key，
 *      name 产品名，eg: T恤
 *      count 产品件数, eg: 1
 *      price 单价（单位分）,eg : 200
 *  {"product":[{"name" : "xxx", "count":1, "price" : 111}, {"name" : "yyy", "count":2, "price" : 222}]}
 */
//$data['analysis'] = (object)array('key' => 'value', 'product' => array(array('name' => 'productA', 'count' => 1, 'price' => 2), array('name' => 'productB', 'count' => 3, 'price' => 4)));

$type = $_GET['type'];
switch($type){
    case 'ALI_WEB' :
        $title = "支付宝即时到账";
        $data["channel"] = "ALI_WEB";
        break;
    case 'ALI_WAP' :
        $title = "支付宝移动网页";
        $data["channel"] = "ALI_WAP";
        //非必填参数,boolean型,是否使用APP支付,true使用,否则不使用
        //$data["use_app"] = false;
        break;
    case 'ALI_QRCODE' :
        $title = "支付宝扫码支付";
        $data["channel"] = "ALI_QRCODE";
        //qr_pay_mode必填 二维码类型含义
        //0： 订单码-简约前置模式, 对应 iframe 宽度不能小于 600px, 高度不能小于 300px
        //1： 订单码-前置模式, 对应 iframe 宽度不能小于 300px, 高度不能小于 600px
        //3： 订单码-迷你前置模式, 对应 iframe 宽度不能小于 75px, 高度不能小于 75px
        $data["qr_pay_mode"] = "0";
        break;
    case 'ALI_OFFLINE_QRCODE' :
        $data["channel"] = "ALI_OFFLINE_QRCODE";
        $title = "支付宝线下扫码";
        require_once 'ali.offline.qrcode/index.php';
        exit();
        break;
    case 'BD_WEB' :
        $data["channel"] = "BD_WEB";
        $title = "百度网页支付";
        break;
    case 'BD_WAP' :
        $data["channel"] = "BD_WAP";
        $title = "百度移动网页";
        break;
    case 'JD_B2B' :
        $data["channel"] = "JD_B2B";
        /*
         * bank_code(int 类型) for channel JD_B2B
        9102    中国工商银行      9107    招商银行
        9103    中国农业银行      9108    光大银行
        9104    交通银行          9109    中国银行
        9105    中国建设银行		9110 	 平安银行
        */
        $data["bank_code"] = 9102;
        $title = "京东B2B";
        break;
    case 'JD_WEB' :
        $data["channel"] = "JD_WEB";
        $title = "京东网页";
        break;
    case 'JD_WAP' :
        $data["channel"] = "JD_WAP";
        $title = "京东移动网页";
        break;
    case 'UN_WEB' :
        $data["channel"] = "UN_WEB";
        $title = "银联网页";
        break;
    case 'UN_WAP' : //由于银联做了适配,需在移动端打开,PC端仍显示网页支付
        $data["channel"] = "UN_WAP";
        $title = "银联移动网页";
        break;
    case 'WX_NATIVE':
        $data["channel"] = "WX_NATIVE";
        $title = "微信扫码";
        require_once 'wx/wx.native.php';
        exit();
        break;
    case 'WX_JSAPI':
        $data["channel"] = "WX_JSAPI";
        $title = "微信公众号";
        require_once 'wx/wx.jsapi.php';
        exit();
        break;
    case 'WX_WAP':
        $data["channel"] = "WX_WAP";
        $title = "微信H5网页";
        //需要参数终端ip，格式如下：
        //$data['analysis'] = (object)array('ip' => $_SERVER['REMOTE_ADDR']);
        break;
    case 'YEE_WEB' :
        $data["channel"] = "YEE_WEB";
        $title = "易宝网页";
        break;
    case 'YEE_WAP' :
        $data["channel"] = "YEE_WAP";
        $data["identity_id"] = "xxxxxxxxxxxxxx";
        $title = "易宝移动网页";
        break;
    case 'YEE_NOBANKCARD':
        //total_fee(订单金额)必须和充值卡面额相同，否则会造成金额丢失(渠道方决定)
        $data["total_fee"] = 10;
        $data["channel"] = "YEE_NOBANKCARD";
        //点卡卡号，每种卡的要求不一样
        $data["cardno"] = "622662180018xxxx";
        //点卡密码，简称卡密
        $data["cardpwd"] = "xxxxxxxxxxxxxx";
        /*
         * frqid 点卡类型编码
         * 骏网一卡通(JUNNET),盛大卡(SNDACARD),神州行(SZX),征途卡(ZHENGTU),Q币卡(QQCARD),联通卡(UNICOM),
         * 久游卡(JIUYOU),易充卡(YICHONGCARD),网易卡(NETEASE),完美卡(WANMEI),搜狐卡(SOHU),电信卡(TELECOM),
         * 纵游一卡通(ZONGYOU),天下一卡通(TIANXIA),天宏一卡通(TIANHONG),32 一卡通(THIRTYTWOCARD)
         */
        $data["frqid"] = "SZX";
        $title = "易宝点卡支付";
        break;
    case 'KUAIQIAN_WEB' :
        $data["channel"] = "KUAIQIAN_WEB";
        $title = "快钱移动网页";
        break;
    case 'KUAIQIAN_WAP' :
        $data["channel"] = "KUAIQIAN_WEB";
        $title = "快钱移动网页";
        break;
    case 'PAYPAL_PAYPAL' :
        $data["channel"] = "PAYPAL_PAYPAL";
        /*
         * currency参数的对照表, 请参考:
         * https://github.com/beecloud/beecloud-rest-api/tree/master/international
         */
        $data["currency"] = "USD";
        $title = "Paypal网页";
        break;
    case 'PAYPAL_CREDITCARD' :
        $data["channel"] = "PAYPAL_CREDITCARD";
        /*
         * currency参数的对照表, 请参考:
         * https://github.com/beecloud/beecloud-rest-api/tree/master/international
         */
        $data["currency"] = "USD";

        $card_info = array(
            'card_number' => '',
            'expire_month' => 1,  //int month
            'expire_year' => 2016, //int year
            'cvv' => 0,           //string
            'first_name' => '', //string
            'last_name' => '',  //string
            'card_type' => 'visa' //string
        );
        $data["credit_card_info"] = (object)$card_info;
        $title = "Paypal信用卡";
        break;
    case 'PAYPAL_SAVED_CREDITCARD' :
        $data["channel"] = "PAYPAL_SAVED_CREDITCARD";
        /*
         * currency参数的对照表, 请参考:
         * https://github.com/beecloud/beecloud-rest-api/tree/master/international
         */
        $data["currency"] = "USD";
        $data["credit_card_id"] = '';
        $title = "Paypal快捷";
        break;
    case 'BC_GATEWAY' :
        $data["channel"] = "BC_GATEWAY";
        /*
         * card_type(string 类型) for channel BC_GATEWAY
         * 卡类型: 1代表信用卡；2代表借记卡
        */
        $data["card_type"] = '1';
        $data["bank"] = "交通银行";
        $title = "BC网关支付";
        break;
    case 'BC_EXPRESS' :
        $data["channel"] = "BC_EXPRESS";
        //银行卡卡号, (选填，注意：可能必填，根据信息提示进行调整)
        //$data["card_no"] = '622662183243xxxx';
        break;
    case 'BC_NATIVE' :
        $data["channel"] = "BC_NATIVE";
        $title = "BC微信扫码";
        require_once 'wx/wx.native.php';
        exit();
        break;
	case 'BC_WX_WAP' : //请在手机浏览器内测试
		$data["channel"] = "BC_WX_WAP";
        //需要参数终端ip，格式如下：
        //$data['analysis'] = (object)array('ip' => $_SERVER['REMOTE_ADDR']);
        $title = "BC微信移动网页支付";
		break;
    case 'BC_WX_SCAN' :
        $data["channel"] = "BC_WX_SCAN";
        $title = "BC微信刷卡";
        $data["auth_code"] = "13022657110xxxxxxxx";
        break;
    case 'BC_WX_JSAPI':
        $data["channel"] = "BC_WX_JSAPI";
        $title = "微信公众号";
        require_once 'wx/wx.jsapi.php';
        exit();
        break;
    case 'BC_ALI_QRCODE' :
        $data["channel"] = "BC_ALI_QRCODE";
        $title = "BC支付宝线下扫码";
        require_once 'ali.offline.qrcode/index.php';
        exit();
        break;
    case 'BC_ALI_SCAN' :
        $data["channel"] = "BC_ALI_SCAN";
        $title = "BC支付宝刷卡";
        $data["auth_code"] = "28886955594xxxxxxxx";
        break;
    case 'BC_ALI_WAP' : //请在手机浏览器内测试
        $data["channel"] = "BC_ALI_WAP";
        $title = "BC支付宝移动网页";
        break;
    case 'BC_QQ_NATIVE' :
        $data["channel"] = "BC_QQ_NATIVE";
        $title = "BCQQ钱包扫码";
        require_once 'bc.qrcode.php';
        exit();
        break;
    case 'BC_JD_QRCODE' :
        $data["channel"] = "BC_JD_QRCODE";
        $title = "BC京东扫码";
        require_once 'bc.qrcode.php';
        exit();
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
    if(in_array($type, array('PAYPAL_PAYPAL', 'PAYPAL_CREDITCARD', 'PAYPAL_SAVED_CREDITCARD'))){
        $result =  $international->bill($data);
    }else if(in_array($type, array('BC_ALI_SCAN', 'BC_WX_SCAN'))){
        $result =  $api->offline_bill($data);
    }else{
        $result =  $api->bill($data);
    }
    if ($result->result_code != 0) {
        echo '<pre>';
        print_r($result);
        exit();
    }
    if(isset($result->url) && $result->url){
        header("Location:$result->url");
        //echo "<script>location.href='{$result->url}'</script>";
    }else if(isset($result->html) && $result->html) {
        echo $result->html;
    }else if(isset($result->code_url) && $result->code_url) { //channel为BC_ALI_WAP
        header("Location:$result->code_url");
    }else if(isset($result->credit_card_id)){
        echo '信用卡id(PAYPAL_CREDITCARD): '.$result->credit_card_id;
    }else if(isset($result->id)){
        echo $type.'支付成功: '.$result->id;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</body>
</html>
