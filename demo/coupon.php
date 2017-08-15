<?php
/**
 * @desc: plan/subscription
 *
 * @author: jason
 * @since: 16/7/26
 */

require_once("../loader.php");
require_once("config.php");

Class CouponsDemo{

	function __construct($app_id, $app_secret, $master_secret, $test_secret) {
		\beecloud\rest\Coupons::registerApp($app_id, $app_secret, $master_secret, $test_secret);
	}

    /*
     * desc: 根据优惠券模板其他条件查询
     * @params $data array:
     *  name string 模板名（如果提供则限制模板名）
     *  created_before long 毫秒数时间戳（如果提供则限制创建时间戳>=该时间戳的模板）
     *  created_after long	毫秒数时间戳（如果提供则限制创建时间戳<该时间戳的模板）
     *  skip long	查询起始位置，默认为0
     *  limit long	查询的条数，默认为10
     *  返回coupon_templates，即优惠券模板列表，此处返回的列表都属于验证签名所用应用app_id下
     *
     */
	function query_temp_bycondition(){
		$data = array(
			'timestamp' => time() * 1000,
			'name' => 'test',
			//'created_before' => 1502768143000,
			//'created_after' => 1502768143000,
			//'skip' => 0,
			//'limit' => 20,
		);
		$temp = \beecloud\rest\Coupons::query_coupon_temp($data);
		if ($temp->result_code != 0) {
			print_r($temp);
			exit();
		}
		print_r($temp->coupon_templates);die;
	}

    /*
     * desc: 根据优惠券模板ID查询
     * @params $objectid string
     *  返回coupon_template，即优惠券模板详情
     */
	function query_temp_byid(){
		$data = array(
			'timestamp' => time() * 1000
		);
		$objectid = '38304e81-4826-4f9d-9898-34255f56400c';
        $temp = \beecloud\rest\Coupons::query_coupon_temp($data, $objectid);
		if ($temp->result_code != 0) {
			print_r($temp);
			exit();
		}
		print_r($temp->coupon_template);die;
	}

    /*
     * 发放卡券
     * @params $data array:
     *  user_id string 用户ID
     *  template_id string 优惠券的模板ID
     */
    function coupon(){
        $data = array(
            'timestamp' => time() * 1000,
            'template_id' => '38304e81-4826-4f9d-9898-34255f56400c',
            'user_id' => 'user_s_2_2017080127'
        );
        $coupon = \beecloud\rest\Coupons::coupon($data);
        if ($coupon->result_code != 0) {
            print_r($coupon);
            exit();
        }
        print_r($coupon->coupon);die;
    }

    /*
     * desc: 根据优惠券其他条件查询
     * @params $data array:
     *  user_id string 用户ID（如果提供则限制领券的用户ID）
     *  template_id string 优惠券的模板ID（如果提供则限制优惠券的模板ID）
     *  status	int	优惠券的状态（如果提供则限制优惠券的状态，0表示未使用，1表示已使用（核销））
     *  limit_fee int 一般传入订单金额，返回满足限额的优惠券，比如传入11000，返回满100元减10元的优惠券
     *  created_before int 毫秒数时间戳（如果提供则限制创建时间戳>=该时间戳的优惠券）
     *  created_after int	毫秒数时间戳（如果提供则限制创建时间戳<该时间戳的优惠券）
     *  skip int	查询起始位置，默认为0
     *  limit int	查询的条数，默认为10
     *  返回coupons，即优惠券列表，此处返回的列表都属于验证签名所用应用app_id下
     *
     */
	function query_coupon_bycondition(){
		$data = array(
			'timestamp' => time() * 1000,
			//'user_id' => '201708110',
			//'template_id' => '15bc0408-9ab5-4dc4-80f5-1c1fc2ec5b83',
			'status' => 1,
			//'limit_fee' => 1000,
			//'created_before' => '1502768143000',
			//'created_after' => '1502768143000',
			//'skip' => 0,
			//'limit' => 20,
		);
        $coupon = \beecloud\rest\Coupons::query_coupon($data);
        if ($coupon->result_code != 0) {
            print_r($coupon);
            exit();
        }
        print_r($coupon->coupons);die;
	}

    /*
     * desc: 根据优惠券ID查询
     * @params $objectid string
     *  返回coupon，即优惠券详情
     */
	function query_coupon_byid(){
		$data = array(
			'timestamp' => time() * 1000
		);
		$objectid = '1602df3f-b871-4787-8cc0-b88d933bdf5c';
        $coupon = \beecloud\rest\Coupons::query_coupon($data, $objectid);
        if ($coupon->result_code != 0) {
            print_r($coupon);
            exit();
        }
        print_r($coupon->coupon);die;
	}
}

try {
	$demo = new CouponsDemo(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);

    $type = isset($_GET['type']) ? $_GET['type'] : 'query_temp_byid';
    switch($type){
        case 'query_temp_byid'://根据ID查询优惠券模板
            $demo->query_temp_byid();
            break;
        case 'query_temp_bycondition': //根据条件查询优惠券模板
            $demo->query_temp_bycondition();
            break;
        case 'query_coupon_byid': //根据ID查询优惠券
            $demo->query_coupon_byid();
            break;
        case 'query_coupon_bycondition': //根据条件查询优惠券
            $demo->query_coupon_bycondition();
            break;
        case 'coupon': //发放优惠券
            $demo->coupon();
            break;
        default:
            exit('No this type!');
            break;
    }
}catch(Exception $e){
	echo $e->getMessage();
}