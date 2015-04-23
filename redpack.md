#微信红包

###申请资质：

* 在[微信公众平台](https://mp.wexin.qq.com)申请公众号->申请成为服务号->申请开通微信支付功能

###需要在BeeCloud后台配置：
![hongbao1](http://beeclouddoc.qiniudn.com/红包1.png)
各参数获取途径：  
1.商户号  
登陆mp.weixin.qq.com
![hongbao2](http://beeclouddoc.qiniudn.com/红包2.png)

2.公众号APPID，支付密钥  
登陆mp.weixin.qq.com
![hongbao3](http://beeclouddoc.qiniudn.com/红包3.png) 

3.红包证书密码, 默认为商户号

4.红包证书  
登陆pay.weixin.qq.com
![honghao4](http://beeclouddoc.qiniudn.com/红包4.png)
会下载下来4个证书，只需将pkcs12格式上传到beecloud后台即可

SDK下载地址:   
JAVA:  [http://beecloud.cn/download/java.php](http://beecloud.cn/download/java.php )   
Python: [http://beecloud.cn/download/python.php](http://beecloud.cn/download/python.php)   
PHP： [http://beecloud.cn/download/php.php](http://beecloud.cn/download/php.php)   
Demo下载地址：  
JAVA：[https://github.com/beecloud/pc-web-pay-demo](https://github.com/beecloud/pc-web-pay-demo)   
Python: [https://github.com/beecloud/beecloud-python](https://github.com/beecloud/beecloud-python)   
PHP:  [https://github.com/beecloud/beecloud-php](https://github.com/beecloud/beecloud-php)  

#PHP

调用

详细见PHP redpackSimpleDemo.php,注意此demo没有处理用户多次触发的情况,仅仅是发送“固定金额”的红包。
随机金额的红包可以通过随机设置total_amount实现,也可以参考 redpackRandomAmountDemo.php使用相关参数实现

配置相关参数，初始化api

```php

set_time_limit(120);
include_once("BCWxmpRedpack.php");
$usrOpenId = "o3kKrjlUsMnv__cK5DYZMl0JoAkY";//用户openId
$appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719"; //BeeCloud appId
$appSecret = ""; //BeeCloud appSecret 为了保密
$appSign = md5($appId.$appSecret);
$mchId = "1234275402";  //微信商户号

//初始化
$api = new BCWxmpApi($appId, $appSecret, $mchId);

```

接收微信xml报文
```php
$postStr = "<xml><ToUserName><![CDATA[gh_71e32cfe546c]]></ToUserName><FromUserName><![CDATA[o3kKrjlUsMnv__cK5DYZMl0JoAkY]]></FromUserName><CreateTime>1429494041</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[抢红包]]></Content><MsgId>6139023951558013395</MsgId></xml>";
//在处理微信请求的服务器上请用如下方式获取真实xml
//$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
$msg = $api->getCallMsg($postStr);//解析xml,获取msg内的参数
```


配置红包,注意**以下参数都不能为空**：


```php

//具体参数意义请参考微信红包文档
$redpack = array(
    "nick_name" => "BeeCloud",
    "send_name" => "BeeCloud",
    "total_amount" => 100, //（分）红包固定金额
    "wishing" => "接入BeeCloud微信红包SDK，就可以实现发放微信红包功能，策划各种脑洞大开的粉丝活动啦！",
    "act_name" => "BeeCloud红包雨",
    "remark" => "BeeCloud",
    /**
     * 此处特别注意count_per_user根据你的需求的设置
     */
    "count_per_user" => 100, //在当前时间t到 t - period时间内每个用户能得到红包个数上限(选填，默认为1)
//    "period" => 300000, //（ms）用户领取红包的判重时间长度,默认为所有时间内
    "probability" => 0.3 //（float）单次获得红包概率 范围0-1, 默认为1
); 
```

发送红包:

~~~php
echo $api->sendRedpack($redpack);
~~~


更详细的处理微信信息过程请参考redpackFullDemo.php中流程, redpackFullDemo.php为实战过的一个样例