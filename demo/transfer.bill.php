<?php
require_once("../loader.php");
require_once("config.php");

$data["timestamp"] = time() * 1000;
$data["total_fee"] = 1;
$data["desc"] = "transfer test";

$type = $_GET['type'];
switch($type) {
    case 'WX_REDPACK' :
        $title = '微信红包'; //单个微信红包金额介于[1.00元，200.00元]之间
        $data["transfer_no"] = "phpdemo".time();//微信要求10位数字
        $data["channel_user_id"] = '';  //微信用户openid o3kKrjlROJ1qlDmFdlBQA95kvbN0
        $data["channel"] = "WX_REDPACK";
        $data["redpack_info"] = (object)array(
            "send_name" => "BeeCloud",
            "wishing" => "test",
            "act_name" => "testA"
        );
        break;
    case 'WX_TRANSFER' :
        $title = '微信企业打款';
        $data["transfer_no"] = "phpdemo".time();//微信企业打款为8-32位数字字母组合
        $data["channel"] = "WX_TRANSFER";
        $data["channel_user_id"] = '';   //微信用户openid o3kKrjlROJ1qlDmFdlBQA95kvbN0
        break;
    case 'ALI_TRANSFER' :
        $title = '支付宝企业打款';
        $data["channel"] = "ALI_TRANSFER";
        $data["transfer_no"] = "phpdemo" . time();

        //收款方的id 账号和 名字也需要对应
        $data["channel_user_id"] = '';   //收款人账户
        $data["channel_user_name"] = ''; //收款人账户姓名

        $data["account_name"] = "苏州比可网络科技有限公司"; //注意此处需要和企业账号对应的全称
        break;
    case 'ALI_TRANSFERS' :
        $title = '支付宝批量打款';
        $data["channel"] = "ALI";
        $data["batch_no"] = "phpdemo" . time();
        $data["account_name"] = "苏州比可网络科技有限公司";
        $data["transfer_data"] = array(
            (object)array(
                "transfer_id" => "bf693b3121864f3f969a3e1ebc5c376a",
                "receiver_account" => "", //收款方账户
                "receiver_name" =>"",     //收款方账号姓名
                "transfer_fee" => 1,      //打款金额，单位为分
                "transfer_note" => "test"
            ),
            (object)array(
                "transfer_id" => "bf693b3121864f3f969a3e1ebc5c3768",
                "receiver_account" => "",
                "receiver_name" =>"",
                "transfer_fee" => 1,
                "transfer_note" => "test"
            )
        );
        break;
    case 'BC_TRANSFER' :
        $title = 'BC企业打款';
        unset($data['desc']);
        $data["bill_no"] = "phpdemo" . $data["timestamp"];
        $data["title"] = 'PHP测试BC企业打款';
        $data["trade_source"] = "OUT_PC";
        /*
         *  如果未能确认银行的全称信息,可通过下面的接口获取并进行确认
         *  //P_DE:对私借记卡,P_CR:对私信用卡,C:对公账户
         *  $banks = $api->bc_transfer_banks(array('type' => 'P_DE'));
         *  if ($result->result_code != 0) {
         *      print_r($result);
         *      exit();
         *  }
         *  print_r($banks->bank_list);die;
         */
        $data["bank_fullname"] = "中国银行"; //银行全称
        $data["card_type"] = "DE"; //银行卡类型,区分借记卡和信用卡，DE代表借记卡，CR代表信用卡，其他值为非法
        $data["account_type"] = "P"; //帐户类型，P代表私户，C代表公户，其他值为非法
        $data["account_no"] = "6222691921993848888";   //收款方的银行卡号
        $data["account_name"] = "test"; //收款方的姓名或者单位名
        //选填mobile
        $data["mobile"] = ""; //银行绑定的手机号
        //选填optional
        $data["optional"] = (object)array("tag"=>"msgtoreturn"); //附加数据

        /**
         * notify_url 选填，该参数是为打款成功之后接收返回信息配置的url,等同于在beecloud平台配置webhook，
         * 如果两者都设置了，则优先使用notify_url。配置时请结合自己的项目谨慎配置，具体请
         * 参考demo/webhook.php
         */
        //$data['notify_url'] = 'http://beecloud.cn';

        break;
    case 'CJ_TRANSFER' :
        $title = '测试畅捷企业打款';
        unset($data['desc']);
        $data["bill_no"] = "phpdemo" . $data["timestamp"];
        $data["title"] = 'PHP测试畅捷企业打款';
        /*
         *  for bank_name, 支持的银行列表名称如下:
         *  ICBC    中国工商银行      ABC     中国农业银行  BOC    中国银行
         *  CCB     中国建设银行      COMM    交通银行      CMB    招商银行
         *  CMBC    中国民生银行      CEB     中国光大银行  CIB   兴业银行
         *  PSBC    中国邮政储蓄银行   GDB    广发银行      SPDB   上海浦东发展银行
         *  SPDB    浦发银行          HXB    华夏银行
         */
        $data["bank_name"] = "中国银行"; //银行全称
        $data["card_type"] = "DEBIT"; //银行卡类型,区分借记卡和信用卡，DEBIT代表借记卡，CREDIT代表信用卡，其他值为非法
        $data["card_attribute"] = "B"; //帐户类型，B代表公户，C代表私户，其他值为非法
        $data["bank_account_no"] = "6222691921993848888";   //收款方的银行卡号
        $data["bank_branch"] = "中国银行独墅湖支行";   //收款方的银行卡号
        $data["account_name"] = "test"; //收款方的姓名或者单位名
        $data["province"] = "江苏省"; //银行所在省份
        $data["city"] = "苏州市"; //银行所在市
        //选填optional
        $data["optional"] = (object)array("tag"=>"msgtoreturn"); //附加数据
        break;
    case 'JD_TRANSFER' :
        $title = 'BC京东代付';
        unset($data['desc']);
        $data["bill_no"] = "phpdemo" . $data["timestamp"];
        $data["channel"] = 'JD_TRANSFER';
        $data["title"] = 'PHP测试京东代付';
        $data["trade_source"] = "OUT_PC";
        /*
         *  如果未能确认银行的全称信息,可通过下面的接口获取并进行确认
         *  //P_DE:对私借记卡,P_CR:对私信用卡,C:对公账户
         *  $banks = $api->bc_transfer_banks(array('type' => 'P_DE'));
         *  if ($result->result_code != 0) {
         *      print_r($result);
         *      exit();
         *  }
         *  print_r($banks->bank_list);die;
         */
        $data["bank_fullname"] = "中国银行"; //银行全称
        $data["card_type"] = "DE"; //银行卡类型,区分借记卡和信用卡，DE代表借记卡，CR代表信用卡，其他值为非法
        $data["account_type"] = "P"; //帐户类型，P代表私户，C代表公户，其他值为非法
        $data["account_no"] = "6226621808888888";   //收款方的银行卡号
        $data["account_name"] = "test"; //收款方的姓名或者单位名
        //选填mobile
        $data["mobile"] = ""; //银行绑定的手机号
        //选填optional
        $data["optional"] = (object)array("tag"=>"msgtoreturn"); //附加数据
        break;
    default :
        exit("No this type.");
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud<?php echo $title; ?>示例</title>
</head>
<body>
<?php
    try {
        //设置app id, app secret, master secret, test secret
        $api->registerApp(APP_ID, APP_SECRET, MASTER_SECRET, TEST_SECRET);

        switch($type){
            case 'ALI_TRANSFERS':
                $result = $api->transfers($data);
                break;
            case 'BC_TRANSFER':
            case 'JD_TRANSFER':
                $result = $api->bc_transfer($data);
                break;
            case 'CJ_TRANSFER':
                $result = $api->cj_transfer($data);
                break;
            default :
                $result = $api->transfer($data);
                break;
        }
        if ($result->result_code != 0) {
            print_r($result);
            exit();
        }
        if(isset($result->url)){
            header("Location:$result->url");
        }else{
            echo '<pre>';
            print_r($result);
            echo '下发成功!';
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>
</body>
</table>
</body>
</html>