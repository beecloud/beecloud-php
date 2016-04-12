<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>根据ID查询订单记录、退款记录</title>
</head>
<body>
<div>
    <h2>根据ID查询订单记录、退款记录</h2>

    <p>请输入ID:</p>
</div>
<form target="_blank">
    <input type="text" name="id" style="width:300px;"/>
    <div style="margin:10px 0px 30px 0px;">
        <input id="queryBIll" type="button" class="button" value="订单查询" />
        <input id="queryRefund" type="button" class="button" value="退款查询" />
    </div>
    <table border="1" cellspacing=0 id="query"></table>
</form>
<script type="text/javascript" src="../../statics/jquery-1.11.1.min.js"></script>
<script>
    $(function(){
        $("#queryBIll").click(function() {
            $('#query').empty();
            var id = $("input[name='id']").val();
            if(!id){
                alert('请输入支付订单唯一标识id');
                return;
            }
            $.getJSON("data/queryById.php", {'id' : id, 'type' : 'bill'}, function(res) {
                if(res.result_code){
                    alert(res.err_detail);
                    return;
                }
                var str = "<tr><th>是否支付</th><th>创建时间</th><th>总价(分)</th><th>渠道类型</th><th>订单号</th><th>订单标题</th></tr>";
                var data = res.data;
                if(data){
                    console.log(data);
                    spay_result = data.spay_result ? '支付' : '未支付';
                    str += "<tr><td>"+spay_result+"</td><td>"+data.create_time+"</td><td>"+data.total_fee+"</td><td>"+data.sub_channel+"</td><td>"+data.bill_no+"</td><td>"+data.title+"</td></tr>";
                }
                $('#query').append(str);
            });
        });

        $("#queryRefund").click(function() {
            $('#query').empty();
            var id = $("input[name='id']").val();
            if(!id){
                alert('请输入退款订单唯一标识id');
                return;
            }
            $.getJSON("data/queryById.php", {'id' : id, 'type' : 'refund'}, function(res) {
                if(res.result_code){
                    alert(res.err_detail);
                    return;
                }
                var str = "<tr><th>退款是否成功</th><th>退款创建时间</th><th>退款号</th><th>订单金额(分)</th><th>退款金额(分)</th><th>渠道类型</th><th>订单号</th><th>退款是否完成</th><th>订单标题</th></tr>";
                var data = res.data;
                if(data){
                    result = data.result ? "成功" : "失败";
                    finish = data.finish ? "完成" : "未完成";
                    str += "<tr><td>"+result+"</td><td>"+data.create_time+"</td><td>"+data.refund_no+"</td><td>"+data.total_fee+"</td><td>"+data.refund_fee+"</td><td>"+data.sub_channel+"</td><td>"+data.bill_no+"</td><td>"+data.finish+"</td><td>"+data.title+"</td></tr>";
                }
                $('#query').append(str);
            });
        });
    });
</script>
</body>
</html>
