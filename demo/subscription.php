<?php
/**
 * @desc: plan/subscription
 *
 * @author: jason
 * @since: 16/7/26
 */

require_once("../loader.php");
require_once("config.php");

Class SubscriptionDemo{

	function __construct($app_id, $app_secret, $master_secret, $test_secret) {
		\beecloud\rest\Subscriptions::registerApp($app_id, $app_secret, $master_secret, $test_secret);
	}

	//获取银行列表
	function banks(){
		$data = array(
			'timestamp' => time() * 1000
		);
		$banks = \beecloud\rest\Subscriptions::banks($data);
		if ($banks->result_code != 0) {
			print_r($banks);
			exit();
		}
		echo '<pre>';
		print_r($banks->common_banks);
		print_r($banks->banks);die;
	}

	//获取手机验证码,
	//返回验证码记录的唯一标识,并且手机端接收到验证码,二者供创建subscription使用
	function sms(){
		$data = array(
			'timestamp' => time() * 1000,
			'phone' => '159621431xx'
		);
		$sms = \beecloud\rest\Subscriptions::sms($data);
		if ($sms->result_code != 0) {
			print_r($sms);
			exit();
		}
		echo '<pre>';
		print_r($sms);die;
	}

	//创建plan
	function create_plan(){
		$data = array(
			'timestamp' => time() * 1000,
			'name' => 'jason\'s plan',
			'fee' => 150,  //fee必须不小于 150分, 不大于5000000分
			'interval' => 'month',
			'currency' => 'CNY',
			'interval_count' => 1,
			'trial_days' => 0,
			'optional' => array('desc' => 'create plan for delete')
		);
		$plan = \beecloud\rest\Subscriptions::plan($data);
		if ($plan->result_code != 0) {
			print_r($plan);
			exit();
		}
		echo '<pre>';
		print_r($plan);die;
	}

	//更新plan的[name/option]
	function update_plan(){
		$data = array(
			'timestamp' => time() * 1000,
			'name' => 'jason\'s plan test',
			'optional' => array('desc' => 'create plan', 'time' => date('Y-m-d H:i:s'))
		);
		$objectid = '4a009b37-c36a-49d3-b011-d13d43535b96';
		$plan = \beecloud\rest\Subscriptions::update_plan($data, $objectid);
		if ($plan->result_code != 0) {
			print_r($plan);
			exit();
		}
		echo '<pre>';
		print_r($plan->id);die;
	}

	//按照条件查询plan
	function query_plan_bycondition(){
		$data = array(
			'timestamp' => time() * 1000,
			'name_with_substring' => '计划',
			//'interval' => 'day',
			//'interval_count' => 1,
			//'trial_days' => 0,
		);
		$plan = \beecloud\rest\Subscriptions::query_plan($data);
		if ($plan->result_code != 0) {
			print_r($plan);
			exit();
		}
		echo '<pre>';
		print_r($plan);die;
	}

	//按照ID查询plan
	function query_plan_byid(){
		$data = array(
			'timestamp' => time() * 1000
		);
		$objectid = '83b3da78-b76c-4740-b250-25e240a6957b';
		$plan = \beecloud\rest\Subscriptions::query_plan($data, $objectid);
		if ($plan->result_code != 0) {
			print_r($plan);
			exit();
		}
		echo '<pre>';
		print_r($plan->plan);die;
	}

	//删除plan
	function del_plan(){
		$data = array(
			'timestamp' => time() * 1000
		);
		$objectid = 'de9bf708-842f-4e8f-a12b-c492f22609e4';
		$plan = \beecloud\rest\Subscriptions::del_plan($data, $objectid);
		if ($plan->result_code != 0) {
			print_r($plan);
			exit();
		}
		echo '<pre>';
		print_r($plan->id);die;
	}

	/*
	 * 创建subscription
	 * 1.card_id 与 {bank_name, card_no, id_name, id_no, mobile} 二者必填其一
	 * 2.card_id 为订阅成功时webhook返回里带有的字段，商户可保存下来下次直接使用
	 * 3.bank_name可参考下述API获取支持银行列表，选择传入
	 */
	function create_subscription(){
		$data = array(
			'timestamp' => time() * 1000,
			'buyer_id' => 'jasonhzy@beecloud.cn',
			'plan_id' => '4a009b37-c36a-49d3-b011-d13d43535b96',
			'sms_id' => 'e76232c5-9f94-475f-a3dc-cba922893e6a',
			'sms_code' => '3035',
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
		$subscription = \beecloud\rest\Subscriptions::subscription($data);
		if ($subscription->result_code != 0) {
			print_r($subscription);
			exit();
		}
		echo '<pre>';
		print_r($subscription->subscription);die;
	}

	//按照条件查询subscription
	function query_subscription_bycondition(){

		$data = array(
			'timestamp' => time() * 1000,
			//'buyer_id' => '订阅',
			//'plan_id' => 'e39f8d8d-3769-4076-bad6-272251854f17',
			//'card_id' => '75021eb5-0d2f-4b1c-9194-8280d89dfb9f'
		);
		$subscription = \beecloud\rest\Subscriptions::query_subscription($data);
		if ($subscription->result_code != 0) {
			print_r($subscription);
			exit();
		}
		echo '<pre>';
		print_r($subscription->subscriptions);die;
	}

	//按照ID查询subscription
	function query_subscription_byid(){
		$data = array(
			'timestamp' => time() * 1000
		);
		$objectid = '95fdfc39-62da-4ad5-ae3d-981c74b63ed8';
		$subscription = \beecloud\rest\Subscriptions::query_subscription($data, $objectid);
		if ($subscription->result_code != 0) {
			print_r($subscription);
			exit();
		}
		echo '<pre>';
		print_r($subscription->subscription);die;
	}

	//更新subscription
	function update_subscription(){
		$data = array(
			'timestamp' => time() * 1000,
			'optional' => array('desc' => 'update subscription'),
			//'buyer_id' => '',
			//'plan_id' => '',
			//'card_id' => '',
			//'amount' => 10,
			//'trial_end' => strtotime('2016-10-08') * 1000
		);
		$objectid = 'a41ed2d0-df0d-4a2e-a629-b5e5acf5b0dd';
		$subscription = \beecloud\rest\Subscriptions::update_subscription($data, $objectid);
		if ($subscription->result_code != 0) {
			print_r($subscription);
			exit();
		}
		echo '<pre>';
		print_r($subscription->id);die;
	}

	//取消subscription
	function cancel_subscription(){
		$data = array(
			'timestamp' => time() * 1000
		);
		$objectid = 'a41ed2d0-df0d-4a2e-a629-b5e5acf5b0dd';
		$subscription = \beecloud\rest\Subscriptions::cancel_subscription($data, $objectid);
		if ($subscription->result_code != 0) {
			print_r($subscription);
			exit();
		}
		echo '<pre>';
		print_r($subscription->id);die;
	}
}

try {
	$demo = new SubscriptionDemo(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);

    $type = isset($_GET['type']) ? $_GET['type'] : '';
    switch($type){
        case 'query_banks': //获取银行列表
            $demo->banks();
            break;
        case 'send_code': //获取手机验证码
            $demo->sms();
            break;
        case 'create_plan'://创建plan
            $demo->create_plan();
            break;
        case 'update_plan': //更新plan
            $demo->update_plan();
            break;
        case 'del_plan': //删除plan
            $demo->del_plan();
            break;
        case 'query_plan_bycondition': //按照条件查询plan
            $demo->query_plan_bycondition();
            break;
        case 'query_plan_byid': //按照ID查询plan
            $demo->query_plan_byid();
            break;
        case 'create_subscription'://创建subscription
            $demo->create_subscription();
            break;
        case 'update_subscription': //更新subscription
            $demo->update_subscription();
            break;
        case 'cancel_subscription': //取消subscription
            $demo->cancel_subscription();
            break;
        case 'query_subscription_bycondition'://按照条件查询subscription
            $demo->query_subscription_bycondition();
            break;
        case 'query_subscription_byid': //按照ID查询subscription
            $demo->query_subscription_byid();
            break;
        default:
            exit('No this type!');
            break;
    }
}catch(Exception $e){
	echo $e->getMessage();
}