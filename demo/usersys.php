<?php
/**
 * @desc: 用户系统API
 *
 * @author: jason
 * @since:  2017-06-22 16:18
 */
require_once("../loader.php");
require_once("config.php");

Class UsersysDemo{

    function __construct($app_id, $app_secret, $master_secret, $test_secret) {
        \beecloud\rest\Usersys::registerApp($app_id, $app_secret, $master_secret, $test_secret);
    }

    /*
     * @desc 单个用户注册接口
     * @params
     *    buyer_id string (必填)	商户为自己的用户分配的ID。可以是email、手机号、随机字符串等。最长32位。在商户自己系统内必须保证唯一
     * @return json
     *      result_code int 	返回码，0为正常
     *      result_msg 	string 	返回信息， OK为正常
     *      err_detail 	string 	具体错误信息
     */
    function register(){
        $data = array(
            'timestamp' => time() * 1000,
            'buyer_id' => 'beecloud'
        );
        return \beecloud\rest\Usersys::register($data);
    }

    /*
     * @desc 批量用户导入接口
     * @params
     *    email string (必填) 用户账号
     *    buyer_ids array (必填) 商户为自己的多个用户分配的IDs。每个ID可以是email、手机号、随机字符串等；最长32位；在商户自己系统内必须保证唯一。
     * @return json
     *      result_code int 	返回码，0为正常
     *      result_msg 	string 	返回信息， OK为正常
     *      err_detail 	string 	具体错误信息
     */
    function import_users(){
        $data = array(
            'timestamp' => time() * 1000,
            'email' => 'xxx@beecloud.cn',
            'buyer_ids' => array('xxxx', 'bcxxx')
        );
        return \beecloud\rest\Usersys::import_users($data);
    }

    /*
     * @desc 商户用户批量查询接口
     * @params
     *    email string (非必填) 用户账号
     *    start_time int (非必填) 起始时间。该接口会返回此时间戳之后创建的用户。毫秒时间戳, 13位
     *    end_time int (非必填) 结束时间。该接口会返回此时间戳之前创建的用户。毫秒时间戳, 13位
     *
     * 注意：如果传入email, 就查询该email下的用户;如果不传email，就查询注册时使用该app_id注册的用户
     * @return json
     *      result_code int 	返回码，0为正常
     *      result_msg 	string 	返回信息， OK为正常
     *      err_detail 	string 	具体错误信息
     *      users 	array 	获取到的用户信息列表
     */
    function query_users(){
        $data = array(
            'timestamp' => time() * 1000,
            'email' => 'xxx@beecloud.cn',
            'start_time' => time() * 1000,
            'end_time' => time() * 1000
        );
        return \beecloud\rest\Usersys::query_users($data);
    }

    /*
     * @desc 历史数据补全接口（批量）。该接口要求用户传入订单号与用户ID的对应关系，该接口会将历史数据中，属于该用户ID的订单数据进行标识。
     * @params
     *    bill_info string (必填), json字符串key为buyer_id，value是订单列表
     *          eg: {"aaa@bb.com":["20170302005"], "xxx@bb.com":["20170302001","20170302002","20170302011"]}
     * @return json
     *      result_code int 	返回码，0为正常
     *      result_msg 	string 	返回信息， OK为正常
     *      err_detail 	string 	具体错误信息
     *      如果更新失败会返回以下信息：
     *          failed_bills array 更新失败的订单信息,可能是部分信息。key是buyer_id, value是隶属于该buyer_id的订单列表
     *      注意：重试时，请依据更新失败返回的失败订单信息进行重试，以避免重复更新历史订单信息
     */
    function supply_bills(){
        $data = array(
            'timestamp' => time() * 1000,
            'bill_info' => json_decode(array('xxx@beecloud.cn' => array('20170302001', '20170302002')))
        );
        return \beecloud\rest\Usersys::supply_bills($data);
    }
}

try {
    $demo = new UsersysDemo(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
    $info = $demo->query_users();
    if ($info->result_code != 0) {
        print_r($info);
        exit();
    }
    echo '<pre>';
    print_r($info);die;
}catch(Exception $e){
    echo $e->getMessage();
}