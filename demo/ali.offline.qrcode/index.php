<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud支付宝线下扫码示例</title>
</head>
<body>
<?php
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
<script type="text/javascript" src="statics/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="statics/qrcode.js"></script>
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
            $.getJSON("ali.offline.qrcode/ali.bill.status.php", {"billNo":billNo}, function(res) {
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
            $.getJSON("ali.offline.qrcode/ali.bill.cancel.php", {"billNo":billNo}, function(res) {
                if(res.resultCode == 0 && res.revert_status){
                    $("#msg").text("支付已经取消");
                    $("#cancel").hide();
                }
            });
        });
    });

</script>
</body>
</html>