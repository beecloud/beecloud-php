#<a name="php">PHP开发指南</a>
#<a name="php_Introduction">微信支付简介</a>
##<a name="php_Function">功能简介</a>
微信支付,是基于微信客户端提供的支付服务功能。同时向商户提供销售经营分析、账 户和资金管理的功能支持。用户通过扫描二维码、微信内打开商品页面购买等多种方式调起 微信支付模块完成支付。  
微信支持公众号内支付,即基于公众号向用户收款,公众号相当亍收款的商户。其中支付方式,可以分为 JS API(网页内)支付、Native(原生)支付。商户可以结合业务场景, 自主选择支付方式。
  
##<a name="php_account">支付账户</a>
商户向微信提交企业以及银行账户资料,商户功能审核通过后,可以获得以下帐户(包含财付通的商户账户),用于公众号支付。  

帐号|作用
---|----
appId|微信公众号身份的唯一标识。审核通过后,在微信发送的邮件中查看。
Mchid|商户 ID,身份标识,在微信发送的邮件中查看。
Key|商户支付密钥 Key。登录微信商户后台,进入栏目【账户设置】【密码安全】 【API 安全】【API 密钥】,进入设置 API 密钥。
Appsecret|JSAPI 接口中获取 openid,审核后在公众平台开启开发模式后可查看。

>注意: 支付密钥 Key 是验证商户唯一性的安全标识,请妥善保管,仅保留在BeeCloud后台和微信后台,不会在网络中传播。

##<a name="php_Methods">支付方式</a>
**JS API(网页内)支付**:是指用户打开图文消息戒者扫描二维码,在微信内置浏览器打 开网页进行的支付。商户网页前端通过使用微信提供的 JS API,调用微信支付模块。这种方式,适合需要在商户网页进行选购下单的购买流程。  

**Native(原生)支付**:是指商户组成符合 Native(原生)支付规则的 URL 链接,用户可通过在会话中点击链接戒者扫描对应的二维码直接进入微信支付模块(客户端界面),即 可进行支付。这种方式,适合无需选购直接支付的购买流程。跟 JSAPI 最大的区别是不经过网页调起支付。

##<a name="php_ready">支付前准备</a>
1.微信公众号中需要**网页授权**，用于用户登陆时获取用户信息,什么是网页授权?请移步微信文档[网页授权获取用户基本信息](http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html)

登陆微信公众号,在**开发者中心**的**接口权限表**的**网页账号**中按照提示设置授权的网页目录,请参考图片所示进行设置:

![接口授权表](http://beeclouddoc.qiniudn.com/spay-wxmp-oauth1.jpg)
![网页账号](http://beeclouddoc.qiniudn.com/spay-wxmp-oauth2.jpg)
![设置网页授权](http://beeclouddoc.qiniudn.com/spay-wxmp-oauth3.jpg)

2.设置授权目录

在 [mp.weixin.qq.com](mp.weixin.qq.com) 登陆后->微信支付->开发配置->支付配置,选择修改（**！！只有服务号才能能申请支付功能！！**）

见下图:

![进入目录授权](http://beeclouddoc.qiniudn.com/spay-wxmp-auth1.png)

在修改菜单中 添加**JS API网页支付**下的**支付授权目录**,如下图:

![支付授权目录](http://beeclouddoc.qiniudn.com/spay-wsmp-auth2.png)

例如ask.BeeCloud.cn使用域名下/wxmp/demo/ 作为微信网页支付目录,那么需要添加ask.beecloud.cn//wxmp/demo/ 为支付授权目录

3.在控制台设置支付要素  
![填写支付要素](http://beeclouddoc.qiniudn.com/wxmp_console_setting.jpg)  
公众号APPID: 微信支付商户资料审核通过后邮件中获得  
微信支付商户号: 微信支付商户资料审核通过后邮件中获得  
![公众号APPID](http://beeclouddoc.qiniudn.com/wxmp_setting2.jpg)
支付密钥: 登录[微信商户后台](https://pay.weixin.qq.com)->账户设置->安全设置->API安全标签下设置密钥  
![支付密钥设置](http://beeclouddoc.qiniudn.com/wxmp_setting1.jpg) 
从下图所示位置获取appSecret
![支付appSecret](http://beeclouddoc.qiniudn.com/wxmp_setting3.jpg) 


#<a name="php_AliIntroduction">支付宝支付简介</a>
##<a name="php_AliMethods">支付方式</a>
**网页支付**:是指用户通过浏览器打开网页进行的支付。商户网页前端通过使用BeeCloud SDK,调用支付宝支付模块。这种方式,适合需要在商户网页进行选购下单的购买流程。 
 
**扫码支付**:是指商户组成符合支付规则的 URL 链接,用户可通过在支付宝客户端扫描二维码直接进入支付模块(客户端界面),即可进行支付。这种方式,适合无需选购直接支付的购买流程。

##<a name="php_AliReady">支付前准备</a>
签约下图中的`即时到帐`产品  
![pc-web-02](http://beeclouddoc.qiniudn.com/pc-web-02.jpg)  
获得下图中的`合作者身份（PID）`和`安全校验码（Key）`  
![pc-web-03](http://beeclouddoc.qiniudn.com/pc-web-03.jpg)
在控制台设置支付要素
![ali_07](http://beeclouddoc.qiniudn.com/ali_07.png)

# <a name="php_UnIntroduction">银联支付简介</a>
## <a name="php_UnMethods">支付方式</a>
**网页支付**:是指用户通过浏览器打开网页进行的支付。商户网页前端通过使用BeeCloud SDK,调用银联支付模块。这种方式,适合需要在商户网页进行选购下单的购买流程。

##<a name="php_UnReady">支付前准备</a>
银联在线支付的接入流程大致如下：
![un_01](http://beeclouddoc.qiniudn.com/un_01.png)
环节说明：  
1、合作洽谈，与客户达成入网意向：包括客户选择、确定接入银联业务或产品、形成业务方案。  
2、签订协议：客户与银联签订合作协议、入网协议、清算协议、提供代理清算协议等。  
3、入网申请：客户以正式方式确认开通的业务信息和要素，以及提供证明客户具备开通该业务相应资质的材料。  
4、业务审核：针对某些特定业务，对客户是否具备开展相应业务资质、业务风险是否可接受以及分配客户开展银联业务的特定身份标识、权限的工作。  
5、技术开发：包括银联为了支持客户相应业务为进行的开发和开发阶段测试，以及客户为实现与银联系统对接而进行的开发和测试工作。  
6、入网测试：在正式投产之前，为验证客户和银联系统应用及技术、业务参数配置的正确性、可用性，降低生产系统运营风险而进行的测试。  
7、投产：包括支持客户业务的生产系统对接成功并技术上线、在系统中业务参数配置生效。  
以上流程环节根据实际需要不同，可进行选择配置。一般情况下，2，3，4，5，6工作可以并行。  

>注：入网流程问题发送邮件至： operation@unionpay.com进行业务申请和咨询，银联有专门的人员进行处理。联系电话：021-50362408。在提交入网申请，进入业务审核阶段的时候，银联的技术客服会将您加入到客服全中，方便您及时地解决遇到的问题。审核通过后会收到“商户入网通知参数信息（请注意保密）--（公司名称）”标题的邮件，邮件中包含了一些商户信息，具体内容在邮件的附件中。

*在控制台上传银联支付要素:*
![un_03](http://beeclouddoc.qiniudn.com/un_03.png)


#<a name="php_SDK">使用PHP S-PAY SDK</a>

##<a name="php_Installation">安装</a>
网站下载源码解压到指定目录,在需要使用的php中引用

例如，使用微信支付
>include "BCWXPay.php";

使用支付宝支付
>include "BCAliPay.php";

##<a name="php_wechatWeb">微信网页内支付场景---JS API(网页内)支付接口</a>
1.首先配置*SDK解压目录*/dependency/WxPayPubHelper/WxPay.pub.config.php参数信息,此文件为原生微信SDK所需,以下列出必填参数

~~~PHP
	<?php
	/**
	* 	配置微信账号信息
	*/
	class WxPayConf_pub {
		//=======【基本信息设置】=====================================
		//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
		const APPID = 'wx419f04c4a731303d';
		//受理商ID，身份标识
		const MCHID = '1234275402';
		//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
		const KEY = '**********************';
		//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
		const APPSECRET = '21e4b4593ddd200dd77c751f4b964963';

		//=======【JSAPI路径设置】===================================
		//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
		const JS_API_CALL_URL = 'http://beecloud.cn/wechat_test2/demo.php';

		//=======【异步通知url设置】===================================
		//异步通知url，商户根据实际开发过程设定,此处设置为BeeCloud的测试用url
		const NOTIFY_URL = 'https://115.28.40.236/1/pay/callback/WeChat';
	
	?>
~~~
	
其中:
	
>*JS__API_CALL_URL*
	
需要设置为集成支付所在的页面，例如我们测试demo代码集成在
	 
>http://beecloud.cn/wechat_test2/demo.php

那么
		
>JS_API_CALL_URL = 'http://beecloud.cn/wechat_test2/demo.php'

根据WxPay.pub.config.php中参数，在BeeCloud控制台中设置对应字段，如下
  ![微信公众号支付设置界面](http://beeclouddoc.qiniudn.com/spay-wsmp-setting.png)
  
  WxPay.pub.config.php中
  
  **APPID** 对应控制台中 **商户ID**
  
  **MCHID** 对应为 **商户号**
  
  **APPSECRET** 对应为 **支付密钥**
  

2.配置*SDK解压目录*/config/BCPayConfig.php 中得BeeCloud账户参数, 以下以BeeCloud demo账户参数为例为例

``` PHP

class BCPayConf {
	static public $appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
	static public $appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
}
```

 appId 和 appSecret在BeeCloud控制台如下页面获取:

 ![App info](http://beeclouddoc.qiniudn.com/spay-app-info)
 
3.初始化BeeCloud S-Pay 微信JS-API功能 

```PHP
	 inlcude_once("BCWXPay.php");
    $pay = new BCWXPay($_GET);//传入get参数用于获取微信的openid
```

>注意 **BCWXPay** 需要 *$_GET* 作为参数

3.配置商品信息

~~~PHP
$pay->configProduct(array(
    "body" => "web wxpay",
    "total_fee" => "1", //总金额单位为分以下非必填参数，商户可根据实际情况选填
//  "sub_mch_id" => "123",//子商户号
//    "device_info" => "android",//设备号
//    "attach" =>"wechao",//附加数据
//    "time_start" => "0",//交易起始时间
//    "time_expire" => "0",//交易结束时间
//    "goods_tag" => "hehe",//商品标记
//    "product_id" => "111"//商品ID
));
~~~

4.在微信JS api - WeixinJSBridge.invoke中插入参数

~~~html
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <title>微信安全支付</title>

    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall() {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $temp = $pay->getJsParams(false);?>,
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    //alert(res.err_code+res.err_desc+res.err_msg);
                }
            );
        }

        function callpay() {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
    </script>
</head>
<body>
<div align="center">
    <button style="width:210px; height:30px; background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >贡献一下</button>
</div>
</body>
</html>
~~~

5.跳转到获得的支付URL就可以开始支付(必须在微信中打开)

页面结果

![demo1](http://beeclouddoc.qiniudn.com/spay-wxmp-webpay-result1.png)
![demo2](http://beeclouddoc.qiniudn.com/spay-wxmp-webpay-result2.png)

在线demo地址:
微信关注公众号 BeeCloud
![beecloud-wechat](http://beecloud.cn/image/commn/wechat.jpg)

发送 "demo-jsapi",点击发送的链接即可体验

6.完整代码(见/demo/wxPayDemo.php)：
>由于需要获得授权目录和code，所以本demo不能在本地测试,请部署至服务器并在微信中测试
 
~~~PHP
<?php
include_once("BCWXPay.php");

$pay = new BCWXPay($_GET);


$pay->configProduct(array(
    "body" => "web wxpay",
    "total_fee" => "1", //总金额单位为分以下非必填参数，商户可根据实际情况选填
//  "sub_mch_id" => "123",//子商户号
//    "device_info" => "android",//设备号
//    "attach" =>"wechao",//附加数据
//    "time_start" => "0",//交易起始时间
//    "time_expire" => "0",//交易结束时间
//    "goods_tag" => "hehe",//商品标记
//    "product_id" => "111"//商品ID
));
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <title>微信安全支付</title>

    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall() {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $temp = $pay->getJsParams(false);?>,
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    //alert(res.err_code+res.err_desc+res.err_msg);
                }
            );
        }

        function callpay() {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
    </script>
</head>
<body>
</br></br></br></br>
<div align="center">
    <button style="width:210px; height:30px; background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >贡献一下</button>
</div>
</body>
</html>
~~~

##<a name="php_ScanCode"> 微信线下扫码购买场景---Native(原生)支付接口</a>
1.首先配置参数信息
>此处和**3.2 微信网页内支付场景---JS API(网页内)支付接口**中配置相同

2.初始化BeeCloud S-Pay 微信QRCODE功能 

```PHP
    inlcude_once("BCWXPay.php");
    $pay = new BCWXQrCode();
```

3.配置商品参数

```PHP

	//微信二维码支付，在任意浏览器打开，通过手机微信扫码支付
	//body 商品描述
	//out_trade_no 商户订单号，32位，商户系统中唯一
	//total_fee 总金额单位为分
	$out_trade_no = WxPayConf_pub::APPID.time();
    
	$pay->configProduct(array(
      "body" => "web wxpay",
      "total_fee" => "1", //以下非必填参数，商户可根据实际情况选填
//    "sub_mch_id" => "123",//子商户号
//    "device_info" => "android",//设备号
//    "attach" =>"wechao",//附加数据
//    "time_start" => "0",//交易起始时间
//    "time_expire" => "0",//交易结束时间
//    "goods_tag" => "hehe",//商品标记
//    "product_id" => "111"//商品ID
));


```
>商户的`out_trade_no`必须全局唯一,调试和生产环境,都需要使用唯一的订单号。注意: 当商户的同一个商户号绑定了公众号支付、小额刷卡、APP支付也需要加标识来区分, 不能出现重复。当发起支付返回失败时,一定要用原订单的 out trade no 而丌能重新生 成新的订单号収起支付,避免同一单重复支付。
  
4.通过获得的qrcode自行生成二维码图片（demo中提供了qrcode.js，是一种用js生成二维码图片的方式，供参考）

~~~PHP
$result = $pay->getOrderResult(false);

//商户根据实际情况设置相应的处理流程
if ($result["return_code"] == "FAIL") {
    //商户自行增加处理流程
    echo "通信出错：".$result['return_msg']."<br>";
    exit();
} elseif($result["result_code"] == "FAIL") {
    //商户自行增加处理流程
    echo "错误代码：".$result['err_code']."<br>";
    echo "错误代码描述：".$result['err_code_des']."<br>";
    exit();
} elseif($result["code_url"] != NULL) {
    //从统一支付接口获取到code_url
    $code_url = $result["code_url"];
    //商户自行增加处理流程
    //......
}
~~~

JS生成二维码

~~~html
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>微信安全支付</title>
</head>
<body>
<div align="center" id="qrcode" >
</div>
<div align="center">
    <p>订单号：<?php echo $out_trade_no; ?></p>
</div>

</body>
<script src="./dependency/WxDemo/qrcode.js"></script>
<script>
    if(<?php echo $result["code_url"] != NULL; ?>) {
        var url = "<?php echo $code_url;?>";
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
    }
</script>
</html>
~~~

5.最终页面
![wxmp-qrcode-demo](http://beeclouddoc.qiniudn.com/spay-wxmp-qrdemo.png)

[在线示例-http://beecloud.cn/wechat_test2/wxQrCodeDemo.php](http://beecloud.cn/wechat_test2/wxQrCodeDemo.php)

4.完整代码(/demo/wxQrCodeDemo.php)：

~~~PHP
<?php
/**
 * Native（原生）支付-模式二-demo
 * ====================================================
 * 商户生成订单，先调用统一支付接口获取到code_url，
 * 此URL直接生成二维码，用户扫码后调起支付。
 *
 */
include_once("BCWXPay.php");
$pay = new BCWXQrCode();
$out_trade_no = WxPayConf_pub::APPID.time();
$pay->configProduct(array(
    "body" => "web wxpay",
    "total_fee" => "1", //总金额单位为分以下非必填参数，商户可根据实际情况选填
    "out_trade_no" => "$out_trade_no"//商户订单号
//  "sub_mch_id" => "123",//子商户号
//    "device_info" => "android",//设备号
//    "attach" =>"wechao",//附加数据
//    "time_start" => "0",//交易起始时间
//    "time_expire" => "0",//交易结束时间
//    "goods_tag" => "hehe",//商品标记
//    "product_id" => "111"//商品ID
));


$result = $pay->getOrderResult(false);

//商户根据实际情况设置相应的处理流程
if ($result["return_code"] == "FAIL") {
    //商户自行增加处理流程
    echo "通信出错：".$result['return_msg']."<br>";
    exit();
} elseif($result["result_code"] == "FAIL") {
    //商户自行增加处理流程
    echo "错误代码：".$result['err_code']."<br>";
    echo "错误代码描述：".$result['err_code_des']."<br>";
    exit();
} elseif($result["code_url"] != NULL) {
    //从统一支付接口获取到code_url
    $code_url = $result["code_url"];
    //商户自行增加处理流程
    //......
}

?>


<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>微信安全支付</title>
</head>
<body>
<div align="center" id="qrcode" >
</div>
<div align="center">
    <p>订单号：<?php echo $out_trade_no; ?></p>
</div>
</body>
<script src="./dependency/WxDemo/qrcode.js"></script>
<script>
    if(<?php echo $result["code_url"] != NULL; ?>) {
        var url = "<?php echo $code_url;?>";
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
    }
</script>
</html>
~~~


##<a name="php_aliWeb">支付宝网页支付</a>
1.配置*SDK解压目录*/config/BCPayConfig.php 中的BeeCloud账户参数, 以下以BeeCloud demo账户参数为例为例

``` PHP

class BCPayConf {
	static public $appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
	static public $appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
}
```

2.初始化BeeCloud 支付宝网页支付

```php
	require_once("BCAliPay.php");
	$pay = new BCAliImmediate();
```

3.配置商品信息,生成支付页面

~~~PHP

//配置商品参数
$data = array(
    "out_trade_no" => "10001",//商户网站订单系统中唯一订单号，必填
    "subject" => "薯片",//订单名称,必填
    "total_fee" => "0.01",//付款金额,必填
    "return_url" => "testurl",//页面跳转同步通知页面路径,必填,即完成支付后跳转页面
    "body" => "很好喝",//订单描述
    "show_url" => ""//商品展示地址,需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html
);


$pay->configProduct($data);

//获取ali 返回的html-<form>
$result = $pay->getImmediateHtml();
print $result;
~~~

代码中的各个参数含义如下： 
 
key | 说明  
---- | -----
return_url | 页面跳转同步通知页面路径，需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
seller_email | 卖家支付宝帐户，（必填）
out\_trade\_no | 商户订单号，商户网站订单系统中唯一订单号，（必填）
subject | 订单名称，（必填）
total_fee | 付款金额，（必填）
body | 订单描述（选填）
show_url | 商品展示地址，需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html（选填）
anti\_phishing\_key | 防钓鱼时间戳（选填）
exter\_invoke\_ip | 客户端的IP地址，非局域网的外网IP地址，如：221.0.0.1（选填）

将`$pay->getImmediateHtml()`返回的内容输出到空白的网页，它会自动跳转到支付宝的收银台。

4.完整代码(/demo/aliImmediateDemo.php)：
>本demo可以在本地测试

```HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCLoud支付宝及时到账示例</title>
</head>
<?php
require_once("BCAliPay.php");
/*
 *************************注意*************************
 * 如果您在接口集成过程中遇到支付宝相关问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */



/**************************请求参数**************************/
$pay = new BCAliImmediate();//初始化BC ali 立即到账 功能

//配置商品参数
$data = array(
    "out_trade_no" => "10001",//商户网站订单系统中唯一订单号，必填
    "subject" => "薯片",//订单名称,必填
    "total_fee" => "0.01",//付款金额,必填
    "return_url" => "testurl",//页面跳转同步通知页面路径,必填,即完成支付后跳转页面
    "body" => "很好喝",//订单描述
    "show_url" => ""//商品展示地址,需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html
);

$pay->configProduct($data);

//获取ali 返回的html-<form>
$result = $pay->getImmediateHtml();
print $result;
    ?>
</body>
</html>

```

##<a name="php_aliPay">支付宝扫码支付</a>
1.配置*SDK解压目录*/config/BCPayConfig.php 中的BeeCloud账户参数, 以下以BeeCloud demo账户参数为例为例

``` PHP

class BCPayConf {
	static public $appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
	static public $appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
}
```

2.初始化 BeeCloud 支付宝扫码功能

``` PHP
	require_once("BCAliPay.php");
	$pay = new BCAliQrCode();
```

2.构造商品参数

```PHP 
$biz_data = '{"goods_info": {"id": "10001","name": "自制白开水","price": "0.01","desc": "贼好喝"},"ext_info": {"single_limit": "2","user_limit": "3","logo_name": "BeeCloud"},"need_address":"F","trade_type":"1","notify_url":"http://beecloud.cn/ali_test/aliqrcode/notify_url.php"}';


$biz_data_obj = json_decode($biz_data);
$data = array("method"=>"add", "biz_data"=>$biz_data_obj);
//method说明
//增加二维码(method=add)
//修改二维码(method=modify)
//暂停二维码(method=stop)
//重新启用二维码(method=restart)
$pay->configProduct($data);
``` 

biz_data参数说明:  

参数|参数名称|类型(字符长度)|参数说明|是否可为空|样例
---|-------|------------|-------|--------|----
trade_type|交易类型|String(1)|1:即时到账</br>2:担保交易</br>当本参数设置为2时,need_address必须为T。|不可空|1
need_address|是否需要收货地址|String(1)|T:需要</br>F:不需要</br>当本参数设置为 T 时,支付宝手 机客户端上会出现让用户填写收 货地址的信息。|不可空|T
goods_info|商品明细|String|商品明细信息,请参见 “4.2.2 goods_info参数说明”。|不可空|详见[goods_info参数样例”
return_url|通知商户下单URL|String(128)|商户生成二维码且用户使用了二维码,创建了一笔交易,支付宝通过该路径通知商户系统下订单。</br>如果为空则不通知商户系统。格式:以“http://”或“https://” 开头。|可空|http://www.test.com/r eturn_url.aspx
notify_url|通知商户支付结果url|String(128)|支付成功后,支付宝通过该路径通知商户支付成功,同时获取商户商品信息。</br>如果为空则不通知户系统。格式:以“http://”或“https://” 开头。</br>说明:支付宝通过何种方式获取商户商品信息,以及获取哪些信息,是在商户和支付宝签约时协商确定的。|可空|http://www.test.com/ notify_url.aspx
query_url|查询商品信息url|String(128)|商户码(友宝售货机码)的情况下,支付宝通过该地址获取商品信息。</br>biz_type=9 时,该参数不能为空。格式:以“http://”或“https://” 开头。|可空|http://www.test.com/ query_url.aspx
ext_info|扩展属性|String|扩展属性,请参见“4.2.3 ext_info参数说明”。|可空|详见“ext_info参数样例”
memo|备注|String(20)|备注信息。|可空|备注

goods_info 参数样例（json）:

```PHP
{
	"id": "123456",
	"name": "商品名称", 
	"price": "11.23", 
	"inventory": "100", 
	"sku_title": "请选择颜色:", 
	"sku": [
		{
			"sku_id": "123456", 
			"sku_name": "白色", 
			"sku_price": "30.20", 
			"sku_inventory": "100"
		}, {
			"sku_id": "123456", 
			"sku_name": "白色", 
			"sku_price": "30.20", 
			"sku_inventory": "100"
		} ],
	"expiry_date": "2012-09-09 01:01:01|2012-09-19 01:02:59",
	"desc": "商品描述" 
}
```

ext_info 参数说明:  


参数|参数名称|类型(长度范围)|参数说明|是否可为空|样例
---|-------|------------|------|---------|----
single_limit|单次购买上限|String|单次购买上限,取值范围1~ 10,默认10。|单次购买上限必须小于或等于单用户购买上限。|可空|1
user_limit|单用户购买上限|String(6)|单用户购买上限,最多6位数字,默认无限制。|可空|1
pay_timeout|支付超时时间|String|支付超时时间,单位为分钟, 最小5分钟最大两天,默认15分钟。|可空|30
logo_name|二维码logo名称|String|二维码logo名称,最多5个汉字或者10个数字/字母.|可空|二维码
ext_field|自定义收集用户信息扩展字段|String|如果商户需要用户下单时提供一些简单的信息,比如手机号、身份证号等,可以通过此字段收集。<br>目前最多支持收集 2 项。 包含以下字段:<br>input_title:输入标题,不可空,长度限制为32个字符(中英文符号都为 1 个字符);<br>input_regex:输入内容, 正则表达式,可为空。<br>手机号<br> ^[1][3-8]+\\\d{9}$<br>邮箱<br> ^\\\w+([-+.]\\\w+)\*@\\\w+([- .]\\\w+)\*\\\\.\\\w+([-.]\\w+)*$<br>身份证 <br>^(\\\d{15}\|\\\d{17}(\\\d\|X\|x)) $|可空|{"input_title": "请输入手 机号码 ","input_regex":"^[1][3-8 ]+\\\d{9}$"}

ext_info 参数样例:  

```python
{
	"single_limit":"1",
	"user_limit":"1", 
	"pay_timeout":"30", 
	"logo_name":"二维码", 
	"ext_field":[
		{
			"input_title":"请输入手机号码", 
			"input_regex":"^[1][3-8]+\\d{9}$"
		},]
}
```

3.调用` $pay->getQrCode()`方法获取支付宝二维码地址和二维码图片地址

```PHP
 
 
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
```

4.完整代码(/demo/aliQrCodeDemo.php)：
>本demo可以在本地测试

```PHP 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCLoud支付宝二维码快捷扫码接口示例</title>
    <script type="text/javascript" src="dependency/qrcode.js"></script>
</head>
<?php
require_once("BCAliPay.php");
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

```

##<a name="php_unWebPay">银联网页支付</a>
1.配置*SDK解压目录*/config/BCPayConfig.php 中的BeeCloud账户参数, 以下以BeeCloud demo账户参数为例为例

``` PHP

class BCPayConf {
	static public $appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719";
	static public $appSecret = "39a7a518-9ac8-4a9e-87bc-7885f33cf18c";
}
```

2.初始化 BeeCloud 银联功能

``` PHP
	include_once("../BCUnPay.php");
	$pay = new BCUnPay();
```

3.配置商品信息,生成支付页面

``` PHP
	$pay->configProduct( array(
    'orderId' => date('YmdHis'),	//*商户订单号
    'traceId' => date('YmdHis'),    //
    'frontUrl' =>  'http://localhost:8085/upacp_sdk_php/demo/gbk/FrontReceive.php',  		//*前台通知地址
    'txnAmt' => '10',		//*交易金额，单位分
    'orderDesc' => "描述" // 商品描述
));

$html = $pay->getWebpay();

if ($html != false) {
    echo $html;
} else {
    echo "null";
}
```


3.完整代码(/demo/unWebpayDemo.php)： 
>本demo可以在本地测试

```php
<?php
header( 'Content-type:text/html;charset=utf-8');

include_once("../BCUnPay.php");

$pay = new BCUnPay();
$pay->configProduct( array(
    'orderId' => date('YmdHis'),	//商户订单号
    'traceId' => date('YmdHis'),    //
    'frontUrl' =>  'http://localhost:8085/upacp_sdk_php/demo/gbk/FrontReceive.php',  		//前台通知地址
    'txnAmt' => '10',		//交易金额，单位分
    'orderDesc' => "描述" // 商品描述
));

$html = $pay->getWebpay();

if ($html != false) {
    echo $html;
} else {
    echo "null";
}

```


##微信红包
请查看 redpack.md





