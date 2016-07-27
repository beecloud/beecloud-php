<?php
/**
 * @desc: plan/subscription
 *
 * @author: jason
 * @since: 16/7/26
 */

require_once("../loader.php");

$app_id = '95d87fff-989c-4426-812c-21408644cf88';
$app_secret = '8aaad136-b899-4793-9564-0ebc72ae86f2';
$master_secret = '688dbe68-a7e9-4f16-850a-21270949afe8';
$test_secret = '6e4cba42-2901-43eb-a6f0-74e2bb18515a';

\beecloud\rest\Subscriptions::registerApp($app_id, $app_secret, $master_secret, $test_secret);

//获取银行列表
$data = array(
	'timestamp' => time()*1000
);
//$banks = \beecloud\rest\Subscriptions::subscription_banks($data);
if ($banks->result_code != 0) {
	print_r($banks);
	exit();
}
//echo '<pre>';
//print_r($banks->common_banks);
//print_r($banks->banks);die;

//获取手机验证码
$data = array(
	'timestamp' => time()*1000,
	'phone' => '15962143194'
);
//$sms = \beecloud\rest\Subscriptions::sms($data);
if ($sms->result_code != 0) {
	print_r($sms);
	exit();
}
//echo '<pre>';
//print_r($sms);die;


//创建plan
$data = array(
	'timestamp' => time()*1000,
	'name' => 'jason\'s plan for delete',
	'fee' => 150,  //fee必须不小于 150分, 不大于5000000分
	'interval' => 'month',
	'currency' => 'CNY',
	'interval_count' => 1,
	'trial_days' => 0,
	'optional' => array('desc' => 'create plan for delete')
);
//$plan = \beecloud\rest\Subscriptions::plan($data);
if ($plan->result_code != 0) {
	print_r($plan);
	exit();
}
//echo '<pre>';
//print_r($plan);die;

//更新plan的[name/option]
$data = array(
	'objectid' => '4a009b37-c36a-49d3-b011-d13d43535b96',
	'timestamp' => time()*1000,
	'name' => 'jason\'s plan test',
	'optional' => array('desc' => 'create plan', 'time' => date('Y-m-d H:i:s'))
);
//$plan = \beecloud\rest\Subscriptions::update_plan($data);
if ($plan->result_code != 0) {
	print_r($plan);
	exit();
}
//echo '<pre>';
//print_r($plan->id);die;

//按照条件查询plan
$data = array(
	'timestamp' => time()*1000,
	'name_with_substring' => '计划',
//	'interval' => 'day',
//	'interval_count' => 1,
//	'trial_days' => 0,
);
//$plan = \beecloud\rest\Subscriptions::query_plan($data);
if ($plan->result_code != 0) {
	print_r($plan);
	exit();
}
//echo '<pre>';
//print_r($plan);

//按照ID查询plan
$data = array(
	'timestamp' => time()*1000,
	'objectid' => '83b3da78-b76c-4740-b250-25e240a6957b'
);
//$plan = \beecloud\rest\Subscriptions::query_plan($data);
if ($plan->result_code != 0) {
	print_r($plan);
	exit();
}
//echo '<pre>';
//print_r($plan->plan);

//删除plan
$data = array(
	'timestamp' => time()*1000,
	'objectid' => 'de9bf708-842f-4e8f-a12b-c492f22609e4'
);
//$plan = \beecloud\rest\Subscriptions::del_plan($data);
if ($plan->result_code != 0) {
	print_r($plan);
	exit();
}
//echo '<pre>';
//print_r($plan->id);die;




/*
 * 创建subscription
 * 1.card_id 与 {bank_name, card_no, id_name, id_no, mobile} 二者必填其一
 * 2.card_id 为订阅成功时webhook返回里带有的字段，商户可保存下来下次直接使用
 * 3.bank_name可参考下述API获取支持银行列表，选择传入
 */

$data = array(
	'timestamp' => time()*1000,
	'buyer_id' => 'jasonhzy@beecloud.cn',
	'plan_id' => '4a009b37-c36a-49d3-b011-d13d43535b96',
	'sms_id' => 'e76232c5-9f94-475f-a3dc-cba922893e6a',
	'sms_code' => '3035',
	//'card_id' => '',
	'bank_name' => '中国银行',
	'card_no' => '6217906101007378888',
	'id_name' => 'jason',
	'id_no' => '413026199011207580',
	'mobile' =>	'15962143194',
	'amount' => 10,
	'trial_end' => strtotime('2016-10-08') * 1000,
	'valid' => true,
	'cancel_at_period_end' => false,
	'optional' => array('desc' => 'create subscription')
);
//$subscription = \beecloud\rest\Subscriptions::subscription($data);
if ($subscription->result_code != 0) {
	print_r($subscription);
	exit();
}
//echo '<pre>';
//print_r($subscription->subscription);die;

//按照条件查询subscription
$data = array(
	'timestamp' => time()*1000,
	'buyer_id' => 'ruitest',
//	'plan_id' => 'e39f8d8d-3769-4076-bad6-272251854f17',
//	'card_id' => '75021eb5-0d2f-4b1c-9194-8280d89dfb9f'
);
//$subscription = \beecloud\rest\Subscriptions::query_subscription($data);
if ($subscription->result_code != 0) {
	print_r($subscription);
	exit();
}
//echo '<pre>';
//print_r($subscription->subscriptions);

//按照ID查询subscription
$data = array(
	'timestamp' => time()*1000,
	'objectid' => '95fdfc39-62da-4ad5-ae3d-981c74b63ed8'
);
//$subscription = \beecloud\rest\Subscriptions::query_subscription($data);
if ($subscription->result_code != 0) {
	print_r($subscription);
	exit();
}
//echo '<pre>';
//print_r($subscription->subscription);

//更新subscription
$data = array(
	'timestamp' => time()*1000,
	'objectid' => 'a41ed2d0-df0d-4a2e-a629-b5e5acf5b0dd',
	'valid' => true
);
//$subscription = \beecloud\rest\Subscriptions::update_subscription($data);
if ($subscription->result_code != 0) {
	print_r($subscription);
	exit();
}
//echo '<pre>';
//print_r($subscription->id);


//取消subscription
$data = array(
	'timestamp' => time()*1000,
	'objectid' => 'a41ed2d0-df0d-4a2e-a629-b5e5acf5b0dd'
);
//$subscription = \beecloud\rest\Subscriptions::cancel_subscription($data);
if ($subscription->result_code != 0) {
	print_r($subscription);
	exit();
}
//echo '<pre>';
//print_r($subscription->id);