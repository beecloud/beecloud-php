<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCLoud支付宝及时到账示例</title>
</head>
<?php
require_once("../BCAliPay.php");
/*
 *************************注意*************************
 * 如果您在接口集成过程中遇到支付宝相关问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */



/**************************请求参数**************************/
$pay = new BCAliImmediate();//初始化BC ali 立即到账 功能

//配置商品参数
$data = array(
    "out_trade_no" => "200000",//商户网站订单系统中唯一订单号，必填，不可重复
    "subject" => "薯片",//订单名称,必填
    "total_fee" => "0.01",//付款金额,必填
    "return_url" => "testurl",//页面跳转同步通知页面路径,必填,即完成支付后跳转页面
    "body" => "很好喝",//订单描述
    "show_url" => ""//商品展示地址,需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html
);

$pay->configProduct($data);

//获取ali 返回的html-<form>
$result = $pay->getImmediateHtml();
if ($result != false) {
    // 如果生成支付页面失败,返回false
    print $result;
} else {
    echo $result;
}
    ?>
</body>
</html>