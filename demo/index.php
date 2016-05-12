<!Doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>BeeCloud支付示例</title>
    <link rel="stylesheet" href="statics/index.css" type="text/css">
</head>
<body>
<div>
    <h2>应付总额： ¥0.01</h2>
    <p>请选择支付方式</p>
</div>
<form action="" method="POST" target="_blank">
    <div>
        <ul class="clear" style="margin-top:20px">
            <li class="clicked" onclick="paySwitch(this)">
                <input type="radio" value="ALI_WEB" name="paytype" checked="checked">
                <img src="http://beeclouddoc.qiniudn.com/ali.png" alt="">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="ALI_WAP" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/aliwap.png" alt="">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="ALI_QRCODE" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/alis.png" alt="">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="WX_NATIVE" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/wechats.png" alt="">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="WX_JSAPI" name="paytype">
                <img src="http://7xavqo.com1.z0.glb.clouddn.com/wechatgzh.png" alt="">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="UN_WEB" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/unionpay.png" alt="">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="JD_WEB" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/jd.png" alt="JD　WEB">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="JD_WAP" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/jdwap.png" alt="JD　WAP">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="BD_WEB" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/bd.png" alt="BD WEB">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="BD_WAP" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/bdwap.png" alt="BD WEB">
            </li>
            <!--<li onclick="paySwitch(this)">
                <input type="radio" value="KUAIQIAN_WEB" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/kq.png" alt="KUAIQIAN WEB">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="KUAIQIAN_WAP" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/kqwap.png" alt="KUAIQIAN WAP">
            </li>-->
            <li onclick="paySwitch(this)">
                <input type="radio" value="YEE_WEB" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/yb.png" alt="YEE WEB">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="YEE_WAP" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/ybwap.png" alt="YEE WAP">
            </li>
            <!--<li onclick="paySwitch(this)">
                <input type="radio" value="YEE_NOBANKCARD" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/ybcard.png" alt="YEE NOBANKCARD">
            </li>-->
            <li onclick="paySwitch(this)">
                <input type="radio" value="PAYPAL_PAYPAL" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/paypal.png" alt="PAYPAL PAYPAL">
            </li>
            <!--<li onclick="paySwitch(this)">
                <input type="radio" value="PAYPAL_CREDITCARD" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/icon_paypal_credit.png" alt="PAYPAL CREDITCARD WEB">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="PAYPAL_SAVED_CREDITCARD" name="paytype">
                <img src="http://beeclouddoc.qiniudn.com/icon_paypal_kuaijiezhifu.png" alt="PAYPAL SAVED CREDITCARD">
            </li>-->

            <li onclick="paySwitch(this)">
                <input type="radio" name="paytype" value="BC_GATEWAY">
                <img src="statics/images/gateway_pay.png" alt="BC GATEWAY" >
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" name="paytype" value="BC_EXPRESS">
                <img src="statics/images/bc_kuaijie.png" alt="BC EXPRESS" >
            </li>
        </ul>
    </div>
    <div>
        <input type="button" id="btn_pay" class="button" value="确认付款">
    </div>
</form>
<hr/>
<div>
    <h2>微信、支付宝企业打款</h2>
    <p>请选择渠道进行操作</p>
    <p>注:单个微信红包金额介于[1.00元，200.00元]之间</p>
</div>
<form method="POST" >
	<div>
    	<ul class="clear">
   		 	<li class="clicked" onclick="paySwitch(this)">
                <input type="radio" value="WX_REDPACK" name="transferType"  checked="checked">
                <img src="http://beeclouddoc.qiniudn.com/wx_redpack.png" alt="微信红包">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="WX_TRANSFER" name="transferType">
                <img src="http://beeclouddoc.qiniudn.com/wx_transfer.png" alt="微信单笔打款">
            </li>
            <!--<li onclick="paySwitch(this)">
                <input type="radio" value="ALI_TRANSFER" name="transferType">
                <img src="http://beeclouddoc.qiniudn.com/ali_transfer.png" alt="支付宝单笔打款">
            </li>
            <li onclick="paySwitch(this)">
                <input type="radio" value="ALI_TRANSFERS" name="transferType">
                <img src="http://beeclouddoc.qiniudn.com/alitransfer.png" alt="支付宝批量打款">
            </li>-->
            <li onclick="paySwitch(this)">
                <input type="radio" value="BC_TRANSFER" name="transferType">
                <img src="http://beeclouddoc.qiniudn.com/icon-companypay.png" alt="BC打款-银行卡">
            </li>
    	</ul>
    </div>
     <div>
        <input type="button" id="play_money" class="button" value="确认打款">
    </div>
</form>
<hr/>

<div>
    <h2>订单查询及发起退款，退款查询，退款状态查询</h2>
    <p>请选择渠道进行操作</p>
</div>
<form method="POST">
    <div>
        <ul class="clear" style="margin-top:20px">
            <li class="clicked" onclick="querySwitch(this)">
                <input type="radio" value="ALI" name="querytype" checked="checked">
                <img src="http://beeclouddoc.qiniudn.com/ali.png" alt="">
            </li>
            <li onclick="querySwitch(this)">
                <input type="radio" value="WX" name="querytype">
                <img src="http://beeclouddoc.qiniudn.com/wechat.png" alt="WX">
            </li>
            <li onclick="querySwitch(this)">
                <input type="radio" value="UN" name="querytype">
                <img src="http://beeclouddoc.qiniudn.com/unionpay.png" alt="UN">
            </li>
            <li onclick="querySwitch(this)">
                <input type="radio" value="JD" name="querytype">
                <img src="http://beeclouddoc.qiniudn.com/jd.png" alt="JD">
            </li>
            <li onclick="querySwitch(this)">
                <input type="radio" value="BD" name="querytype">
                <img src="http://beeclouddoc.qiniudn.com/bd.png" alt="BAIDU">
            </li>
            <!--<li onclick="querySwitch(this)">
                <input type="radio" value="KUAIQIAN" name="querytype">
                <img src="http://beeclouddoc.qiniudn.com/kq.png" alt="KUAIQIAN">
            </li>-->
            <li onclick="querySwitch(this)">
                <input type="radio" value="YEE" name="querytype">
                <img src="http://beeclouddoc.qiniudn.com/yb.png" alt="YEE">
            </li>
            <li onclick="querySwitch(this)">
                <input type="radio" value="PAYPAL" name="querytype">
                <img src="http://beeclouddoc.qiniudn.com/paypal.png" alt="PAYPAL">
            </li>
            <li onclick="querySwitch(this)">
                <input type="radio" name="querytype" value="BC">
                <img src="statics/images/bc.png" alt="BC" >
            </li>
        </ul>
    </div>
    <div>
        <input id="queryBIll" type="button" class="button" style="display:inline;" value="订单查询">
        <input id="queryRefund" type="button" class="button" style="display:inline;" value="退款查询">
        <input id="queryApprovalRefund" type="button" class="button" style="display:inline;" value="预退款查询">
    </div>
</form>

<hr/>

<div>
    <h2>根据ID查询订单记录、退款记录</h2>
    <p>请输入ID:</p>
</div>
<form method="POST">
    <div></div>
    <div style="margin-bottom: 20px;">
        <input type="text" name="id" style="display:block;width:300px;height:25px">
        <input id="billQueryById" type="button" class="button" style="display:inline;" value="订单查询">
        <input id="refundQueryById" type="button" class="button" style="display:inline;" value="退款查询">
    </div>
</form>
</body>
<script type="text/javascript" src="statics/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
    function paySwitch(that) {
        var li = that.parentNode.children;
        for (var i = 0; i < li.length; i++) {
            li[i].className = "";
            li[i].childNodes[1].removeAttribute("checked");
        }
        that.className = "clicked";
        that.childNodes[1].setAttribute("checked", "checked");
    }
    function querySwitch(that) {
        var li = that.parentNode.children;
        for (var i = 0; i < li.length; i++) {
            li[i].className = "";
            li[i].childNodes[1].removeAttribute("checked");
        }
        that.className = "clicked";
        that.childNodes[1].setAttribute("checked", "checked");
    }

    $("#btn_pay").click(function(){
        var type = $("input[name='paytype']:checked").val();
        if(!type){
            alert("请选择支付方式");return;
        }
        window.open('./pay.bill.php?type=' + type);
    });

    $("#queryBIll").click(function(){
        var type = $("input[name='querytype']:checked").val();
        if(!type){
            alert("请选择渠道方式");return;
        }
        window.open('./bill.query.php?type=' + type);
    });

    $("#queryRefund").click(function(){
        var type = $("input[name='querytype']:checked").val();
        if(!type){
            alert("请选择渠道方式");return;
        }
        window.open('./refund.query.php?type=' + type);
    });

    $("#queryApprovalRefund").click(function(){
        var type = $("input[name='querytype']:checked").val();
        if(!type){
            alert("请选择渠道方式");return;
        }
        window.open('./preprefund.query.php?type=' + type);
    });



    $("#billQueryById").click(function(){
        var id = $("input[name='id']").val();
        if(!id){
            alert('请输入订单唯一标识id');
            return;
        }
        window.open('./query.byid.php?type=bill&id=' + id);
    });

    $("#refundQueryById").click(function(){
        var id = $("input[name='id']").val();
        if(!id){
            alert('请输入退款订单唯一标识id');
            return;
        }
        window.open('./query.byid.php?type=refund&id=' + id);
    });

    $("#play_money").click(function(){
        var type = $("input[name='transferType']:checked").val();
        if(!type){
            alert("请选择渠道方式");return;
        }
        window.open('./transfer.bill.php?type=' + type);
    });
</script>
</html>
