<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud支付宝线下扫码示例</title>
</head>
<body>
<?php
require_once("../../../loader.php");

$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "ALI_OFFLINE_QRCODE";
$data["total_fee"] = 1;
$data["bill_no"] = "bcdemo" . $data["timestamp"];
$data["title"] = "白开水";
$data["return_url"] = "http://payservice.beecloud.cn";

//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));
//选填 show_url
//$data["qr_pay_mode"] = 0;

try {
    $result = $api->offline_bill($data);
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }
    $code = $result->code_url;
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}
?>
<div style="width:100%; height:100%;overflow: auto; text-align:center;">
    <div id="qrcode"></div>
    <div id="msg"></div>
    <button id="cancel">取消支付</button>
</div>
<script type="text/javascript" src="../../../statics/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../../../statics/qrcode.js"></script>
<script>
    if("<?php echo $code != NULL; ?>") {
        var options = {text: "<?php echo $code;?>"};
        var canvas = BCUtil.createQrCode(options);
        var wording=document.createElement('p');
        wording.innerHTML = "线下扫码支付";
        var element=document.getElementById("qrcode");
        element.appendChild(wording);
        element.appendChild(canvas);
    }

    $(function(){
        var billNo = "<?php echo $data["bill_no"];?>";
        var queryTimer = setInterval(function() {
            $("#msg").text("开始查询支付状态...");
            $.getJSON("ali.bill.status.php", {billNo:billNo}, function(res) {
                if(res.resultCode == 0 && res.pay_result){
                    clearInterval(queryTimer);
                    queryTimer = null;
                    $("#msg").text("已经支付");
                    $("#cancel").hide();
                }
            });
        }, 3000);
        $("#cancel").click(function() {
            if (queryTimer) {
                clearInterval(queryTimer);
                queryTimer = null;
            }
            $("#qrcode").empty();
            $("#msg").text("支付取消。。。");
            $.getJSON("ali.bill.status.php", {billNo:billNo}, function(res) {
                console.log(res);
                $("#msg").text("支付已经取消");
            });

        });
    });

</script>
</body>
</html>