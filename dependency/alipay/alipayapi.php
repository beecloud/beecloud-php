<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>支付宝支付宝二维码管理接口接口</title>
	<script type="text/javascript" src="qrcode.js"></script>
</head>
<?php
/* *
 * 功能：支付宝二维码管理接口接入页
 * 版本：3.3
 * 修改日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */

require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");

/**************************请求参数**************************/

        //接口调用时间
        $timestamp = date('Y-m-d h:i:s');
        //格式为：yyyy-MM-dd HH:mm:ss

        //动作
        $method = "add";
        //创建商品二维码
        //业务类型
        $biz_type = "10";
        //目前只支持1
        //业务数据
//        $biz_data = $_POST['WIDbiz_data'];
        //格式：JSON 大字符串，详见技术文档4.2.1章节

		$biz_data = '{"goods_info": {
				"id": "10001",
				"name": "自制白开水",
				"price": "0.01",
				"desc": "贼好喝"
				},
				"ext_info": {
						"single_limit": "2",
				"user_limit": "3",
				"logo_name": "BeeCloud"
				},
				"need_address":"F",
				"trade_type":"1",
				"notify_url":"http://beecloud.cn/ali_test/aliqrcode/notify_url.php"
				}';

/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "alipay.mobile.qrcode.manage",
		"partner" => trim($alipay_config['partner']),
		"timestamp"	=> $timestamp,
		"method"	=> $method,
		"biz_type"	=> $biz_type,
		"biz_data"	=> $biz_data,
		"_input_charset"  => trim(strtolower($alipay_config['input_charset']))
);

//$data = array("method"=>"add", "biz_data"=>$biz_data);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestHttp($parameter);

//解析XML
//注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
$xmlNodes = simplexml_load_string($html_text);
//print_r($xmlNodes);
$qrcode =  $xmlNodes->response->alipay->qrcode->asXML();
$qrurl =  $xmlNodes->response->alipay->qrcode_img_url->asXML();


//请在这里加上商户的业务逻辑程序代码

//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
if (! empty($qrcode) ) {
	?>
	<div id="qrcode">

	</div>
	<script>
		var url = "<?php echo strip_tags($qrcode);?>";
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


//解析XML
if( ! empty($qrurl) ) {
	?>

	<div>
		<img src="<?php echo strip_tags($qrurl);?>">
	</div>
    <?php
}

//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

?>
</body>
</html>