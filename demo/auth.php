<?php
/**
 * @desc: 鉴权
 *
 * @author: jason
 * @since: 16/7/26
 */

require_once("../loader.php");

Class AuthDemo{

	function __construct($app_id, $app_secret, $master_secret, $test_secret) {
		\beecloud\rest\Auths::registerApp($app_id, $app_secret, $master_secret, $test_secret);
	}

	//二要素,三要素,四要素鉴权,如果鉴权成功，会自动在全局的card表中创建一条card记录
	function auth(){
		$data = array(
			'timestamp' => time() * 1000,
			'name' => 'jason',
			'card_no' => '6227856101009660xxx',
			'id_no' => '23082619860124xxxx',
			'mobile' => '1555551xxxx'
		);
		return \beecloud\rest\Auths::auth($data);
	}
}

try {
	$app_id = '95d87fff-989c-4426-812c-21408644cf88';
	$app_secret = '8aaad136-b899-4793-9564-0ebc72ae86f2';
	$master_secret = '688dbe68-a7e9-4f16-850a-21270949afe8';
	$test_secret = '6e4cba42-2901-43eb-a6f0-74e2bb18515a';

	$demo = new AuthDemo($app_id, $app_secret, $master_secret, $test_secret);
	//三要素，四要素鉴权，如果鉴权成功，会自动在全局的card表中创建一条card记录
	$auth = $demo->auth();
	if ($auth->result_code != 0) {
		print_r($auth);
		exit();
	}
	echo '<pre>';
	print_r($auth);die;
}catch(Exception $e){
	echo $e->getMessage();
}