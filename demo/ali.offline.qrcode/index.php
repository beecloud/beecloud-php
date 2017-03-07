<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo $title; ?>示例</title>
</head>
<body>
<?php
try {
    switch ($data['channel']){
        case 'BC_ALI_QRCODE':
            $result = $api->bill($data);
            break;
        case 'ALI_OFFLINE_QRCODE':
            $result = $api->offline_bill($data);
            break;
        default:
            exit('channel must be BC_ALI_QRCODE  or ALI_OFFLINE_QRCODE');
            break;
    }
    if ($result->result_code != 0) {
        print_r($result);
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
    <?php  if($data['channel'] != 'BC_ALI_QRCODE'){ ?>
        <button id="cancel">取消支付</button>
    <?php } ?>
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
            $.getJSON("bill.status.php", {"billNo":billNo, 'channel' : '<?php echo $data['channel']; ?>'}, function(res) {
                if(res.result_code == 0){
                    if(res.pay_result){
                        clearInterval(queryTimer);
                        queryTimer = null;
                        $("#cancel").hide();
                    }
                    $("#msg").text(res.pay_result ? '已经支付' : '未支付');
                }
            });
        }, 3000);

        if("<?php echo $data['channel'] != 'BC_ALI_QRCODE'; ?>"){
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
        }
    });

</script>
</body>
</html>