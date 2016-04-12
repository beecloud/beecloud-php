<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud Paypal网页支付</title>
</head>
<body>
    <div>
        <h2>cardinfo method</h2>
        card_number: <input id="card_number"/>
        expire_month: <input id="expire_month"/>
        expire_year: <input id="expire_year" />
        cvv: <input id="cvv" />
        first_name: <input id="first_name" />
        last_name: <input id="last_name" />
        card_type: <input id="card_type" value="visa"/>
        <button id="cardinfo-btn">付$0.01</button>
        <p id="info-result"></p>
    </div>

    <div>
        <h2>cardid method</h2>
        card_id: <input id="card_id"/>
        <button id="cardid-btn">付$0.01</button>
        <p id="id-result"></p>
    </div>

    <div>
        <h2>login paypal to pay</h2>
        <button id="paypal">Paypal login to pay</button>
    </div>
    <script type="text/javascript" src="../../statics/jquery-1.11.1.min.js"></script>
    <script>
        $(function() {
            $("#paypal").click(function() {
                $.post("data/paypal.php",function(result){
                    console.log(result);
                    console.log(result.log)
                    if (result.resultCode == 0) {
                        window.location.href = result.url;
                    }
                }, 'json');

            });

            $("#cardinfo-btn").click(function() {
                var cardinfo = {
                    card_number : $("#card_number").val(),
                    expire_month : parseInt($("#expire_month").val()),
                    expire_year : parseInt($("#expire_year").val()),
                    cvv : parseInt($("#cvv").val()),
                    first_name : $("#first_name").val(),
                    last_name: $("#last_name").val(),
                    card_type: $("#card_type").val()
                }
                $("#info-result").empty();
                $.post("data/paypal.creditinfo.php",{cardinfo: JSON.stringify(cardinfo)},function(result){
                    $("#info-result").text(JSON.stringify(result));
                    console.log(result);
                }, 'json');

            });

            $("#cardid-btn").click(function() {
                $("#id-result").empty();
                $.post("data/paypal.creditid.php",{cardId: $("#card_id").val()},function(result){
                    $("#id-result").text(JSON.stringify(result));
                    console.log(result);
                }, 'json');
            });
        })
    </script>
</body>
</html>