<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud 根据ID查询记录示例</title>
    <link type="text/css" rel="stylesheet" href="statics/demo.css" />
</head>
<body>
<table border="1" align="center" cellspacing=0>
    <?php
    require_once("../loader.php");
    require_once("config.php");

    $data["timestamp"] = time() * 1000;
    $data["id"] = $_GET['id'];
    $type = $_GET['type'];

    if(empty($data["id"])){
        exit(json_encode(array('result_code' => 1, 'err_detail' => '请输入id')));
    }
    try {
        //设置app id, app secret, master secret, test secret
        $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);
        //Test Model,只提供下单和支付订单查询的Sandbox模式,不写setSandbox函数或者false即live模式,true即test模式
        $api->setSandbox(false);

        switch($type){
            case 'bill':
                $result = $api->bill($data, 'get');
                if ($result->result_code != 0 || $result->result_msg != "OK") {
                    print_r($result);
                    exit();
                }
                $data = $result->pay;
                $str = "<tr><th>是否支付</th><th>创建时间</th><th>总价(分)</th><th>渠道类型</th><th>订单号</th><th>订单标题</th></tr>";
                if($data){
                    $create_time = isset($data->create_time) && $data->create_time ? date('Y-m-d H:i:s', $data->create_time/1000) : '';
                    $spay_result = $data->spay_result ? '支付' : '未支付';
                    $str .= "<tr><td>$spay_result</td><td>$create_time</td><td>$data->total_fee</td><td>$data->sub_channel</td><td>$data->bill_no</td><td>$data->title</td></tr>";
                }
                echo $str;
                break;
            case 'refund':
                $result = $api->refund($data, 'get');
                if ($result->result_code != 0 || $result->result_msg != "OK") {
                    print_r($result);
                    exit();
                }
                $data = $result->refund;
                $str = "<tr><th>退款是否成功</th><th>退款创建时间</th><th>退款号</th><th>订单金额(分)</th><th>退款金额(分)</th><th>渠道类型</th><th>订单号</th><th>退款是否完成</th><th>订单标题</th></tr>";
                if($data){
                    $create_time = isset($data->create_time) && $data->create_time ? date('Y-m-d H:i:s', $data->create_time/1000) : '';
                    $result = $data->result ? "成功" : "失败";
                    $finish = $data->finish ? "完成" : "未完成";
                    $str .= "<tr><td>$result</td><td>$create_time</td><td>$data->refund_no</td><td>$data->total_fee</td><td>$data->refund_fee</td><td>$data->sub_channel</td><td>$data->bill_no</td><td>$finish</td><td>$data->title</td></tr>";
                }
                echo $str;
                break;
        }
    } catch (Exception $e) {
        die($e->getMessage());
    }
    ?>
</table>
</body>
</html>