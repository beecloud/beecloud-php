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


#JAVA
调用

```java
//启动BeeCloud
BeeCloud.registerApp(“your BeeCloud app id”, bcAppSecret);
```

```java
//billno string 格式： mch_id（微信商户id，10位）+日期（yyyyMMdd）+ 10位数字（每天唯一）共计28位，如1234567890201504180000000001
//openid string 红包接受者的微信openid
//total_fee int 以分为单位，必须是100的整数倍，最多20000分
BCPay.sendRedPack(billno, openid,
                total_fee, "这里填昵称", "这里填发送方名称", "这里是祝福语", "这里填活动名称", "这里填备注");
```

demo: 
 
```java
import cn.beecloud.BCPay;
import cn.beecloud.BeeCloud;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Map;
import java.util.Random;

public class Test {
    public static void main(String[] args) {
        String bcAppId = ""; // beecloud appid, beecloud 后台提供
        String bcAppSecret = ""; // beecloud appsecret, beecloud 后台提供
        String mch_id = "";   // 微信商户号， mp.weixin.qq.com可查到

        BeeCloud.registerApp(bcAppId, bcAppSecret); // 初始化beecloud

        String billSuf = billSuf();
        String billno = mch_id + billSuf;
        System.out.println(billSuf.length());
        Map<String, Object> ret = BCPay.sendRedPack(billno, "o3kKrjlUsMnv__cK5DYZMl0JoAkY",
                100, "这里填昵称", "这里填发送方名称", "这里是祝福语", "这里填活动名称", "这里填备注");
        System.out.println(ret);
        System.exit(0);
    }

    private static String billSuf() {
        SimpleDateFormat ff = new SimpleDateFormat();
        ff.applyPattern("yyyyMMdd");
        String date = ff.format(new Date());
        long ts = System.currentTimeMillis();
        String timeStr = ("" + ts).substring(6);
        Random random = new Random();
        int a = random.nextInt(100) + 100;
        return date + timeStr + a;
    }
}
```

#Python
调用：

```python
//启动BeeCloud
BCApi.bc_app_id = 'your app id'
BCApi.bc_app_secret = 'your app secret'
api = BCApi()

//发起红包
//billno string 格式： mch_id（微信商户id，10位）+日期（yyyyMMdd）+ 10位数字（每天唯一）共计28位，如1234567890201504180000000001
//openid string 红包接受者的微信openid
//total_fee int 以分为单位，必须是100的整数倍，最多20000分
api.bc_red_pack(billno, openid, total_fee, "这里填昵称", "这里填发送方名称", "这里是祝福语", "这里填活动名称", "这里填备注")
```

demo:

```python
BCApi.bc_app_id = 'c5d1cba1-5e3f-4ba0-941d-9b0a371fe719'
BCApi.bc_app_secret = '39a7a518-9ac8-4a9e-87bc-7885f33cf18c'
api = BCApi()

mch_id = '1234275402'
now = datetime.datetime.now()
date = now.strftime("%Y%m%d")
data = api.bc_red_pack(mch_id + date + no, 'o3kKrjsL1LAGguIrCKsTtFGxo-zg', 100, 'nick', 'nick', '中文', 'act', 'remark')
```

#PHP

调用

详细见PHP redpackSimpleDemo.php,注意此demo没有处理用户多次触发的情况,仅仅是发送红包

配置相关参数

```php

set_time_limit(120);
include_once("BCWxmpRedpack.php");

$usrOpenId = "o3kKrjsL1LAGguIrCKsTtFGxo-zg";//用户openId
$appId = "c5d1cba1-5e3f-4ba0-941d-9b0a371fe719"; //BeeCloud appId
$appSecret = ""; //BeeCloud appSecret 为了保密
$appSign = md5($appId.$appSecret); 
$mchId = "1234275402";  //微信商户号

```


配置红包,注意**以下参数都不能为空**：


```php

//具体参数意义请参考微信红包文档
$redpack = array(
    "nick_name" => "BeeCloud",
    "send_name" => "BeeCloud",
    "total_amount" => 100,
    "wishing" => "接入BeeCloud微信红包SDK，就可以实现发放微信红包功能，策划各种脑洞大开的粉丝活动啦！",
    "act_name" => "BeeCloud红包雨",
    "remark" => "BeeCloud",
    "count_per_user" => 1 //beecloud 中限制每个用户获得的红包数目
//    "period" => 300000, 
//    "probability" => 0.3
);
    
```

发送红包:

~~~php

$beecloud = new stdClass();
$beecloud->appId = $appId;
$beecloud->appSign = $appSign;
$beecloud->mchId = $mchId;

//以下echo只为了本地调试时打印，在微信中打印请参考redpackFullDemo.php中得流程
echo BCWxmpApi::sendWxmpRedpack(BCWxmpRedPackSetting::getServerRandomUrl(), $usrOpenId, $redpack, $beecloud, 30);
~~~


更详细demo请参考redpackFullDemo.php中流程