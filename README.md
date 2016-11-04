## BeeCloud PHP SDK (Open Source)

[![Build Status](https://travis-ci.org/beecloud/beecloud-php.svg)](https://travis-ci.org/beecloud/beecloud-php) ![license](https://img.shields.io/badge/license-MIT-brightgreen.svg) ![version](https://img.shields.io/badge/version-v2.3.0-blue.svg)

## 简介

本项目的官方GitHub地址是 [https://github.com/beecloud/beecloud-php](https://github.com/beecloud/beecloud-php)，本SDK 是基于 [BeeCloud RESTful API](https://github.com/beecloud/beecloud-rest-api)开发的PHP SDK，目前支持以下功能：
- 微信支付、支付宝支付、银联在线支付、百度钱包支付、京东支付、PayPal等多种支付方式
- 支付/退款订单总数的查询
- 支付订单和退款订单的查询
- 根据ID(支付/退款订单唯一标识)查询订单记录、退款记录

依赖: PHP 5.3+, PHP-curl

## 流程

下图为整个支付的流程: 
![Flow](http://7xavqo.com1.z0.glb.clouddn.com/img-beecloud%20sdk.png)

步骤①：**（从网页服务器端）发送订单信息**  
步骤②：**收到BeeCloud返回的渠道支付地址（比如支付宝的收银台）**  
>*特别注意：
微信扫码返回的内容不是和支付宝一样的一个有二维码的页面，而是直接给出了微信的二维码的内容，需要用户自己将微信二维码输出成二维码的图片展示出来*

步骤③：**将支付地址展示给用户进行支付**  
步骤④：**用户支付完成后通过一开始发送的订单信息中的return_url来返回商户页面**
>*特别注意：
微信没有自己的页面，二维码展示在商户自己的页面上，所以没有return url的概念，需要商户自行使用一些方法去完成这个支付完成后的跳转（比如后台轮询查支付结果）*

此时商户的网页需要做相应界面展示的更新（比如告诉用户"支付成功"或"支付失败")。**不允许**使用同步回调的结果来作为最终的支付结果，因为同步回调有极大的可能性出现丢失的情况（即用户支付完没有执行return url，直接关掉了网站等等），最终支付结果应以下面的异步回调为准。

步骤⑤：**（在商户后端服务端）处理异步回调结果（[Webhook](https://beecloud.cn/doc/?index=webhook)）**
 
付款完成之后，根据客户在BeeCloud后台的设置，BeeCloud会向客户服务端发送一个Webhook请求，里面包括了数字签名，订单号，订单金额等一系列信息。客户需要在服务端依据规则要验证**数字签名是否正确，购买的产品与订单金额是否匹配，这两个验证缺一不可**。验证结束后即可开始走支付完成后的逻辑。

## 初始化

1. 注册开发者: BeeCloud平台[注册账号](http://beecloud.cn/register/)
2. 创建应用: 使用注册的账号登陆,在控制台中创建应用,点击**"+添加应用"**创建新应用,具体可参考[快速开始](https://beecloud.cn/apply/)
3. 获取参数: 在新创建的应用中即可获取APP ID,APP Secret,Master Secret,Test Secret
4. 在代码中调用方法registerApp(请注意各个参数一一对应):

```
/* registerApp fun four params
 * @param(first) $app_id beecloud平台的APP ID
 * @param(second) $app_secret  beecloud平台的APP SECRET
 * @param(third) $master_secret  beecloud平台的MASTER SECRET
 * @param(fouth) $test_secret  beecloud平台的TEST SECRET, for sandbox
 */
\beecloud\rest\api::registerApp('app id', 'app secret', 'master secret', 'test secret');
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
BCRESTApi::registerApp('app id', 'app secret', 'master secret', 'test secret')
```

5. LIVE模式和TEST模式

在代码中调用方法setSandbox, 即:
- setSandbox(false)或者不调用此方法, 即LIVE模式
- setSandbox(true), 即TEST模式, 仅提供下单和支付订单查询的Sandbox模式

开启测试模式,即:
```
\beecloud\rest\api::setSandbox(true);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
BCRESTApi::setSandbox(true)
```

## 引入BeeCloud API

### 使用[composer](https://getcomposer.org/)
> composer 是php的包管理工具， 通过composer.json里的配置管理依赖的包，同时可以在使用类时自动加载对应的包

在你的composer.json中添加如下依赖

```
{
	"require": {
		"beecloud.cn/rest": "{version}"
	}
}
```


version ＝ dev-master为主干分支开发版本,请酌情使用

然后命令行执行

```
composer install
```

在需要使用的php文件中引入vendor/autoload.php

```
require_once('vendor/autoload.php');
```

### 手动使用
适合不能使用composer(PHP < 5.3.2)或者namespace(PHP <
5.3)的情况，拷贝当前所有文件(demo可以忽略)到你指定的目录<YourPath>下，你的代码中

```
require_once('<YourPath>/loader.php');
```

## BeeCloud API
- 请求参数和返回参数请参考BeeCloud RESTfull API，同时可以参考demo中各渠道的代码示例
- [常见问题](https://beecloud.cn/faq/)
- [错误对照码](https://github.com/beecloud/beecloud-rest-api/tree/master/error_code)

## 发起支付订单

### 国际支付

国际支付目前主要是PayPal支付方式，主要提供了三种支付渠道类型：

- 当channel参数为PAYPAL_PAYPAL，即PayPal立即支付，接口返回url，用户跳转至此url，登陆paypal便可完成支付
- 当channel参数为PAYPAL_CREDITCARD，即信用卡支付，直接支付成功，接口返回的信用卡ID，此ID在快捷支付时需要
- 当channel参数为PAYPAL_SAVED_CREDITCARD，即存储的信用卡id支付，直接支付成功

支付调用的方法：

```
\beecloud\rest\international::bill(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTInteraional::bill(array $data);
```

注：具体的请求参数和返回参数，请参考[国际支付REST API](https://github.com/beecloud/beecloud-rest-api/tree/master/international)

### 国内支付

国内支付适用于支付宝支付、京东支付、微信支付、百度钱包支付、银联在线支付等多种支付方式，选择不同的支付渠道类型，请求参数和返回参数也不尽相同，开发过程中要特别留意，严格按照提供的[线上支付REST API](https://github.com/beecloud/beecloud-rest-api/tree/master/online)文档进行开发。


支付调用方法：

```
\beecloud\rest\api::bill(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::bill(array $data);
```

注：具体的请求参数和返回参数，请参考[线上支付REST API](https://github.com/beecloud/beecloud-rest-api/tree/master/online) **【2. 支付】**部分

## 支付订单查询

用户提供相应的请求参数，并调用api中的bills方法，即可进行查询，主要包括单个（根据ID查询，即订单的唯一标识，而不是bill\_no）和多条记录的查询

多条记录的查询调用方法：

```
\beecloud\rest\api::bills(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::bills(array $data);
```

根据ID查询调用的方法：

```
\beecloud\rest\api::bill(array $data, 'get');
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::bill(array $data, 'get');
```


注：具体的请求参数和返回参数，请参考[线上支付REST
API](https://github.com/beecloud/beecloud-rest-api/tree/master/online) **【5.订单查询】【11.
支付订单查询(指定ID)】**部分

## 订单总数查询

该接口主要用于对订单总数的统计，其中我们可以对其中的一段时间（即设置start\_time、end\_time）订单的统计，也可以只统计成功支付的订单（即设置spay\_result为true即可）

调用的方法：

```
\beecloud\rest\api::bills_count(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::bills_count(array $data);
```

注：具体的请求参数和返回参数，请参考[线上支付REST
API](https://github.com/beecloud/beecloud-rest-api/tree/master/online) **【6. 订单总数查询】**部分

## 发起退款

退款接口仅支持对已经支付成功的订单进行退款，对于同一笔订单，仅能退款成功一次（同一个退款请求，第一次退款申请被驳回，可进行第二次退款申请）。
退款金额refund\_fee必须小于或者等于原始支付订单的total\_fee，如果是小于，则表示部分退款

退款接口包含预退款功能，当need\_approval值为true时，该接口开启预退款功能，预退款仅创建退款记录，并不真正发起退款，需后续调用审核接口，或者通过BeeCloud控制台的预退款界面，审核同意或者否决，才真正发起退款或者拒绝预退款。

退款调用方法：
```
\beecloud\rest\api::refund(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::refund(array $data);
```

注：具体的请求参数和返回参数，请参考[线上支付REST
API](https://github.com/beecloud/beecloud-rest-api/tree/master/online) **【3. 退款】**部分


## 预退款批量审核

批量审核接口仅支持预退款，批量审核分为批量驳回和批量同意

调用方法：
```
\beecloud\rest\api::refund(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::refund(array $data);
```

注：具体的请求参数和返回参数，请参考[线上支付REST
API](https://github.com/beecloud/beecloud-rest-api/tree/master/online) **【4. 预退款批量审核】**部分


## 退款订单查询

用户提供相应的请求参数，并调用api中的refunds方法，即可进行查询，主要包括单个（根据ID查询，即退款订单的唯一标识，而不是refund\_no）和多条记录的查询

多条记录查询调用的方法：
```
\beecloud\rest\api::refunds(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::refunds(array $data);
```

根据ID查询调用的方法：

```
\beecloud\rest\api::refund(array $data, 'get');
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::refund(array $data, 'get');
```

注：具体的请求参数和返回参数，请参考[线上支付REST
API](https://github.com/beecloud/beecloud-rest-api/tree/master/online) **【7. 退款查询】【10.
退款订单查询(指定ID)】**部分


## 退款总数查询

该接口主要用于对退款订单总数的统计，其中我们可以对其中的一段时间（即设置start\_time、end\_time）退款订单的统计，也可以统计是否是预退款的退款订单（即设置need\_approval为true即可）

调用的方法：

```
\beecloud\rest\api::refunds_count(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::refunds_count(array $data);
```

注：具体的请求参数和返回参数，请参考[线上支付REST
API](https://github.com/beecloud/beecloud-rest-api/tree/master/online) **【8. 退款总数查询】**部分

## 退款状态更新

退款状态更新接口提供查询退款状态以更新退款状态的功能，用于对退款后不发送回调的渠道（WX、YEE、KUAIQIAN、BD）退款后的状态更新。

调用方法：

```
\beecloud\rest\api::refundStatus(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::refundStatus(array $data);
```

注：具体的请求参数和返回参数，请参考[线上支付REST
API](https://github.com/beecloud/beecloud-rest-api/tree/master/online) **【9. 退款状态更新】**部分

## BeeCloud企业打款 - 打款到银行卡

调用方法：
```
\beecloud\rest\api::bc_transfer(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::bc_transfer(array $data);
```

注：具体的请求参数和返回参数，请参考[企业打款REST API](https://github.com/beecloud/beecloud-rest-api/tree/master/transfer) **【BeeCloud企业打款 - 打款到银行卡】**部分

## 微信企业打款/微信红包

主要包括微信红包、微信企业打款

打款调用方法：

```
\beecloud\rest\api::transfer(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用

```
BCRESTApi::transfer(array $data);
```

注：具体的请求参数和返回参数，请参考[企业打款REST
API](https://github.com/beecloud/beecloud-rest-api/tree/master/transfer) **【微信企业打款/微信红包】**部分

## 鉴权

二要素{name(身份证姓名), id_no(身份证号)}，三要素{name(身份证姓名), id_no(身份证号), card_no(用户银行卡卡号)}，四要素{name(身份证姓名), id_no(身份证号), card_no(用户银行卡卡号), mobile(手机号)}鉴权，
如果鉴权成功，会自动在全局的card表中创建一条card记录.

### 初始化

在代码中调用方法registerApp(请注意各个参数一一对应), 具体实现可参考demo/auth.php:

```
/* registerApp fun four params
 * @param(first) $app_id beecloud平台的APP ID
 * @param(second) $app_secret  beecloud平台的APP SECRET
 * @param(third) $master_secret  beecloud平台的MASTER SECRET
 * @param(fouth) $test_secret  beecloud平台的TEST SECRET, for sandbox
 */
\beecloud\rest\Auths::registerApp('app id', 'app secret', 'master secret', 'test secret');
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Auths::registerApp('app id', 'app secret', 'master secret', 'test secret')
```
### 鉴权接口


调用方法:
```
\beecloud\rest\Auths::auth(array $data);
```

不使用namespace的用户和2.2.0之前的v2版本用户请使用
```
Auths::auth(array $data);
```

请求参数列表如下:

参数名 | 类型 | 含义 | 描述 | 例子 | 是否必填
----  | ---- | ---- | ---- | ---- | ----
app_id | String | BeeCloud平台的AppID | App在BeeCloud平台的唯一标识 | 0950c062-5e41-44e3-8f52-f89d8cf2b6eb | 是
timestamp | Long | 签名生成时间 | 时间戳，毫秒数 | 1435890533866 | 是
app_sign | String | 加密签名 | 算法: md5(app\_id+timestamp+app\_secret)，32位16进制格式,不区分大小写 | b927899dda6f9a04afc57f21ddf69d69 | 是
name | String | 身份证姓名 |-  | 王小明 | 是
id_no | String | 身份证号 | - | 2308****32 | 是
card_no   | String | 用户银行卡卡号   |-  | 6217xxxxx3402 | 是
mobile | String | 手机号 | - | 133****3156 | 否

请求返回结果,json格式:
```
{
	"card_id": "xxx",
	"auth_result": true,
	"auth_msg": "xxx不匹配"   //auth_result为false时返回失败信息   
}
```


## 订阅

### 初始化

在代码中调用方法registerApp(请注意各个参数一一对应), 具体实现可参考demo/subscription.php:

```
/* registerApp fun four params
 * @param(first) $app_id beecloud平台的APP ID
 * @param(second) $app_secret  beecloud平台的APP SECRET
 * @param(third) $master_secret  beecloud平台的MASTER SECRET
 * @param(fouth) $test_secret  beecloud平台的TEST SECRET, for sandbox
 */
\beecloud\rest\Subscriptions::registerApp('app id', 'app secret', 'master secret', 'test secret');
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::registerApp('app id', 'app secret', 'master secret', 'test secret')
```	

### 计划(plan)

关于计划的说明, 具体的可参考[订阅系统说明文档](https://github.com/beecloud/beecloud-rest-api/blob/master/subscription/%E8%AE%A2%E9%98%85%E7%B3%BB%E7%BB%9F%E8%AF%B4%E6%98%8E%E6%96%87%E6%A1%A3.md)

- 一项计划由每期费用、周期、试用天数等参数构成。
- 周期两个要素：周期间隔单位(interval, 可以是天、周、月、年)和间隔数量(interval_count),
- 商户可以灵活组合“周期间隔单位”和“间隔数量”以满足周期性收费需求,eg:设置每三个月的扣费场景,即interval=month，interval_count=3,

		interval为month, interval_count最大是12; 
		interval为week, interval_count最大是52; 
		interval为year, interval_count最大是1;
		
- 商户根据业务需求，设定每期费用(fee)
- "试用天数(trial_days)"用于调整该项计划的首次扣费时间，默认值为0，如果设置了，表示某用户从注册订阅到开始被收费的间隔天数为trial_days。
见如下计划的周期性扣费时间点的示例（按月计费，不考虑订阅中的trial_end）：
      
		无试用期，先付费后使用：trial_days设为0会从订阅成功的下一天开始扣费，以后按月扣费。
		无试用期，先使用后付费：trial_days设为当月天数（假设为30），从订阅成功的第31天起按月扣费。
		有试用期3天，先付费后使用：trial_days设为3，从订阅成功的第4天开始扣费，以后按月扣费。
		有试用期3天，先使用后付费：trial_days需要设置为 3 + 月的天数(假设30)，从订阅成功的第34天扣费，以后按月扣费。

#### 创建计划

创建计划调用的方法:

```
$data = array(
	'timestamp' => time() * 1000, 		//毫秒, 必填, 用于验签
	'name' => 'jason\'s plan',		//必填, 计划名称
	'fee' => 150,  				//必填, 单位分, fee必须不小于 150分, 不大于5000000分
	'currency' => 'CNY', 			//默认为CNY，表示人民币，目前仅支持CNY
	'interval' => 'month', 			//收费周期单位，只能是day、week、month、year
	'interval_count' => 1, 			//间隔数量, 和interval共同定义收费周期,默认1
	'trial_days' => 0,			//试用天数,默认为0
	'optional' => array('desc' => 'create plan')
);
\beecloud\rest\Subscriptions::plan($data);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::plan($data)
```

#### 查询计划

按照ID查询

```
$data = array(
	'timestamp' => time() * 1000 //毫秒, 必填, 用于验签
);
$objectid = '83b3da78-b76c-4740-b250-25e240a6xxxx'; //必填, 该计划的唯一标识
\beecloud\rest\Subscriptions::query_plan($data, $objectid);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::query_plan($data, $objectid)
```

按照条件查询

```
$data = array(
	'timestamp' => time() * 1000 	//毫秒, 必填, 用于验签
	'name_with_substring' => 'xxx', //按照计划名称模糊查询
	'interval' => 'day',		//按照收费周期单位查询
	'interval_count' => 2,		//按照间隔数量查询
	'trial_days' => 10		//按试用天数查询
);
\beecloud\rest\Subscriptions::query_plan($data);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::query_plan($data)
```
#### 修改计划

```
$data = array(
	'timestamp' => time() * 1000,		//毫秒, 必填, 用于验签
	'name' => 'jason\'s plan update',	//修改计划名称
	'optional' => array('desc' => 'update plan')
);
$objectid = '83b3da78-b76c-4740-b250-25e240a6xxxx'; //必填, 该计划的唯一标识
\beecloud\rest\Subscriptions::update_plan($data, $objectid);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::update_plan($data, $objectid)
```

#### 删除计划

```
$data = array(
	'timestamp' => time() * 1000 //毫秒, 必填, 用于验签
);
$objectid = '83b3da78-b76c-4740-b250-25e240a6xxxx'; //必填, 该计划的唯一标识
\beecloud\rest\Subscriptions::del_plan($data, $objectid);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::del_plan($data, $objectid)
```

### 订阅(subscription)

关于订阅的说明, 具体的可参考[订阅系统说明文档](https://github.com/beecloud/beecloud-rest-api/blob/master/subscription/%E8%AE%A2%E9%98%85%E7%B3%BB%E7%BB%9F%E8%AF%B4%E6%98%8E%E6%96%87%E6%A1%A3.md)

- 订阅需要传入buyer_id，可以是用户email，也可以是商户系统中的用户ID，由商户系统决定该id的唯一性。
- 当前的订阅允许同一个用户多次订阅同一个计划，当多次发起订阅请求的时候请务必确认这不是错误操作，因为每一次成功的订阅都会收取用户的费用。
- 对于类似收取电费的场景，计划的收费金额fee应当是电费的单价，用户每月使用的度数在订阅中的amount设置，在每次扣款时间点之前，商户的系统需要更新每个注册用户对应订阅的amount数值。
- 商户根据需要设定“试用截止时间点(trial_end, 默认值null)”，此时收费时间请参阅计划中trial_days的说明。如果设置了，计划中的“试用天数(trial_days)”将被忽略，当前订阅直接从trial_end的下一天进行第一次扣费，之后按照计划中设定的时间间隔，周期性扣费。该参量可以用来统一订阅用户的收费时间。
- 用户第一次注册成功的情况下，webhook会返回card_id，该id由{bank_name、card_no、id_name、id_no、mobile}共同决定，可以直接用于发起订阅。

#### 获取银行列表

获取银行列表, 成功后会返回banks和common_banks两种, 可查看银行的名称,用于创建订阅使用

```
$data = array(
	'timestamp' => time() * 1000 //毫秒, 必填, 用于验签
);
\beecloud\rest\Subscriptions::banks($data);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::banks($data, $objectid)
```

#### 获取短信验证码

获取短信验证码, 成功后会返回sms_id(验证码记录的唯一标识),并且手机端可接收到验证码,二者供创建subscription使用

```
$data = array(
	'timestamp' => time() * 1000,	//毫秒, 必填, 用于验签,
	'phone' => '159621431xx' 	//手机号, 必填, 用于接收验证码
);
\beecloud\rest\Subscriptions::sms($data);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::sms($data)
```

#### 创建订阅

对于首次创建订阅的用户,需要用户输入账户要素{bank_name(银行名称), card_no(银行卡号), id_name(身份证姓名), id_no(身份证号), mobile(银行预留手机号)},
其中银行名称,可通过上述获取**获取银行列表**接口获取。另外还需参数sms_id(可通过上述获取**获取短信验证码**接口获取)和sms_code(发送到手机验证码)

创建订阅需要注意:
- 1.card_id 与 {bank_name, card_no, id_name, id_no, mobile} 二者必填其一
- 2.card_id 为订阅成功时webhook返回里带有的字段，商户可保存下来下次直接使用
```
$data = array(
	'timestamp' => time() * 1000,
	'buyer_id' => 'jasonhzy@beecloud.cn',
	'plan_id' => '4a009b37-c36a-49d3-b011-d13d4353xxxx',
	'sms_id' => 'e76232c5-9f94-475f-a3dc-cba92289xxxx',
	'sms_code' => 'xxxx',
	//'card_id' => '',
	'bank_name' => '中国银行',
	'card_no' => '621790610100737xxxx',
	'id_name' => 'jason',
	'id_no' => '41302619901120xxxx',
	'mobile' => '1596214xxxx',
	'amount' => 10,
	'trial_end' => strtotime('2016-10-08') * 1000,
	'valid' => true,
	'cancel_at_period_end' => false,
	'optional' => array('desc' => 'create subscription')
);
\beecloud\rest\Subscriptions::subscription($data);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::subscription($data)
```

#### 查询订阅

按照ID查询

```
$data = array(
	'timestamp' => time() * 1000 //毫秒, 必填, 用于验签
);
$objectid = '95fdfc39-62da-4ad5-ae3d-981c74b6xxxx'; //必填, 该订阅的唯一标识
\beecloud\rest\Subscriptions::query_subscription($data, $objectid);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::query_subscription($data, $objectid)
```

按照条件查询

```
$data = array(
	'timestamp' => time() * 1000			//毫秒, 必填, 用于验签
	'buyer_id' => 'xxxx',				//按照订阅的buyer ID查询
	'plan_id' => 'e39f8d8d-3769-4076-bad6-27225185xxxx',  //按照计划ID查询
	'card_id' => '75021eb5-0d2f-4b1c-9194-8280d89dxxxx'   //按照card ID查询
);
\beecloud\rest\Subscriptions::query_subscription($data);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::query_subscription($data)
```

#### 修改订阅

```
$data = array(
	'timestamp' => time() * 1000,
	'optional' => array('desc' => 'update subscription'),
	'buyer_id' => '',
	'plan_id' => '',
	'card_id' => '',
	'amount' => 10,
	'trial_end' => strtotime('2016-10-08') * 1000
);
$objectid = '95fdfc39-62da-4ad5-ae3d-981c74b6xxxx'; //必填, 该订阅的唯一标识
\beecloud\rest\Subscriptions::update_subscription($data, $objectid);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::update_subscription($data, $objectid);
```

#### 取消订阅

```
$data = array(
	'timestamp' => time() * 1000
);
$objectid = '95fdfc39-62da-4ad5-ae3d-981c74b6xxxx'; //必填, 该订阅的唯一标识
\beecloud\rest\Subscriptions::cancel_subscription($data, $objectid);
//不使用namespace的用户和2.2.0之前的v2版本用户请使用
Subscriptions::cancel_subscription($data, $objectid);
```

## Demo

项目文件夹demo为我们的样例项目,可根据自己的需要做出相应的调整

- 微信没有return_url，如果用户需要支付完成做类似同步跳转的形式，需根据微信支付提供的jsapi完成。
- 关于支付宝支付、银联在线支付、百度钱包支付、京东支付、PayPal等支付方式的return_url,需要用户自己设定
- 关于webhook的接收 请参考demo中的webhook.php, 文档请阅读 [webhook](https://github.com/beecloud/beecloud-webhook)
- 关于订阅接收webhook推送,有两个推送:

    	1.订阅结果的推送,transaction_id就是创建订阅时返回的订阅id，transaction_type为SUBSCRIPTION，sub_channel_type为BC_SUBSCRIPTION，message_detail中包含用户相关的注册信息，其中的card_id注意留存, 可供创建订阅使用;
    	2.订阅收费结果的推送，transaction_id为收费订单记录的订单号bill_no，transaction_type为PAY，sub_channel_type为BC_SUBSCRIPTION，transaction_fee为本次收费金额，message_detail中包含用户相关的注册信息
-  关于代扣接收webhook推送,有两个推送:

    	1.代扣签约的推送,transaction_id就是创建代扣签约时返回的id，transaction_type为SIGN，sub_channel_type为BC_CARD_CHARGE，message_detail中包含签约的详细信息，其中的card_id注意留存, 可供支付时使用;
    	2.代扣支付结果的推送，transaction_id为收费订单记录的订单号bill_no，transaction_type为PAY，sub_channel_type为BC_CARD_CHARGE，transaction_fee为代扣支付金额，message_detail中包含签约的详细信息
-  关于企业打款接收webhook推送: 
        
        推送企业打款结果,transaction_id就是企业打款的交易单号, 对应支付请求的bill_no, transaction_type为TRANSFER, sub_channel_type为BC_TRANSFER, message_detail中包含打款相关的详细信息
        


## 测试

项目文件夹tests为我们的样例测试,可根据自己的需要做出相应的调整

- 测试采用PHPUnit,请参考[PHPUnit](https://phpunit.de/)

## 联系我们
- 如果发现了bug，欢迎提交[issue](https://github.com/beecloud/beecloud-php/issues)
- 如果有新的需求，欢迎提交[issue](https://github.com/beecloud/beecloud-php/issues)

## 代码许可
The MIT License (MIT).
