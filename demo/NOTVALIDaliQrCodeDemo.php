<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCLoud支付宝二维码快捷扫码接口示例</title>
    <script type="text/javascript" src="../dependency/qrcode.js"></script>
</head>
<?php
require_once("../BCAliPay.php");
/*
 *************************注意*************************
 * 如果您在接口集成过程中遇到支付宝相关问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */



/**************************请求参数**************************/

$pay = new BCAliQrCode();


$biz_data = '{"goods_info": {"id": "10001","name": "自制白开水","price": "0.01","desc": "贼好喝"},"ext_info": {"single_limit": "2","user_limit": "3","logo_name": "BeeCloud"},"need_address":"F","trade_type":"1","notify_url":"http://beecloud.cn/ali_test/aliqrcode/notify_url.php"}';


$biz_data_obj = json_decode($biz_data);
$data = array("method"=>"add", "biz_data"=>$biz_data_obj);
$pay->configProduct($data);

$result = $pay->getQrCode();

if ($result != false && ! empty($result["qrcode"]) ) {
    ?>
    <div id="qrcode">

    </div>
    <script>
        var url = "<?php echo $result["qrurl"];?>";
        //参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
        var qr = qrcode(10, 'H');
        qr.addData(url);
        qr.make();
        var wording=document.createElement('p');
        wording.innerHTML = "扫我，扫我";
        var code=document.createElement('DIV');
        code.innerHTML = qr.createImgTag();
        var element=document.getElementById("qrcode");
        element.appendChild(wording);
        element.appendChild(code);
    </script>
<?php
}

if($result != false && ! empty($result["qrurl"]) ) {
    ?>

    <div>
        <img src="<?php echo $result["qrurl"];?>">
    </div>
<?php
}

//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

?>
</body>
</html>