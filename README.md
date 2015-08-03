##BeeCloud RESTfull API - PHP SDK
![license](https://img.shields.io/badge/license-MIT-brightgreen.svg) ![version](https://img.shields.io/badge/version-v2.0.0-blue.svg)

本SDK 基于 [BeeCloud RESTfull API](https://github.com/beecloud/beecloud-rest-api)

依赖:PHP 5.3+, PHP-curl

### 使用前准备
1. BeeCloud[注册](http://beecloud.cn/register/)账号
2. BeeCloud中创建应用，填写支付渠道所需参数

使用中遇到的问题欢迎来QQ群-321545822提问


### 引入BeeCloud API

拷贝beecloud.php 到你指定的目录<YourPath>下，你的代码中
	~~~
		require_once("<YourPath>/beecloud.php");
	~~~

### BeeCloud API （$data参数和返回参数请参考BeeCloud RESTfull API,同时可以参考demo中各渠道的代码示例）
1. 发起支付订单 

	~~~
		BCRESTApi::bill(array $data);
	~~~
2. 查询支付订单

	~~~
		BCRESTApi::bills(array $data);
	~~~
	
3. 发起退款 

	~~~
		BCRESTApi::refund(array $data);
	~~~
	
4. 退款状态查询

	~~~
		BCRESTApi::refunds(array $data);
	~~~
	
5. 退款状态更新(仅微信需要) 

	~~~
		BCRESTApi::refundStatus(array $data);
	~~~


### 其他
1. 相关问题和bug请开issue
2. 代码贡献请提交Pull Request

