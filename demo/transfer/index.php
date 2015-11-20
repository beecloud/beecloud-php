<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 11/20/15
 * Time: 10:30
 */
?>

<html>
<head>
    <meta  charset="UTF-8">
</head>
<body>
    <h2>金额(单位分)</h2>
    <input id="total-fee" placeholder="金额">

    <h2>支付宝打款</h2>
    <input id="ali-user-id" placeholder="收款支付宝账号">
    <input id="ali-user-name" placeholder="收款人账户名">
    <button id="ali">打款</button>
    <br>
    <a href="#" id="ali-link"></a>

    <h2>微信红包和转账</h2>
    <input id="openid" placeholder="微信用户openid">
    <button id="wx-red">发送红包</button>
    <button id="wx-transfer">发送转账</button>
    <script>
        /*
         * 创建XMLHttpRequest的方法
         * @function createXMLHttpRequest
         * @return XMLHttpRequest对象
         */
        var ajaxRequest = function (){
            if (window.ActiveXObject){
                return new ActiveXObject("Microsoft.XMLHTTP");
            }else if(window.XMLHttpRequest){
                return new XMLHttpRequest();
            }
        }();
        ajaxRequest.onreadystatechange= function() {
            if (ajaxRequest.readyState == 4) {
                var data = JSON.parse(ajaxRequest.responseText);
                if (data.resultCode == 0 && !data.url) {
                    alert("下发成功");
                } else if (data.resultCode == 0 && !!data.url) {
                    alert("请在出现的支付宝链接中完成打款");
                    var aliLink = document.getElementById("ali-link");
                    aliLink.textContent =  data.url;
                    aliLink.setAttribute("href", data.url);
                }
            }
        }

        document.getElementById("ali").onclick = function() {
            var totalFee = document.getElementById("total-fee").value;
            var userId = document.getElementById("ali-user-id").value;
            var userName = document.getElementById("ali-user-name").value;
            console.log(totalFee);
            console.log(userId);
            console.log(userName);

            ajaxRequest.open('GET','data/transfer.php?type=ali&amount=' + totalFee + "&userid=" + userId + "&username=" + userName,true);
            ajaxRequest.setRequestHeader('CONTENT-TYPE','application/x-www-form-urlencoded');
            ajaxRequest.send();
        }


        document.getElementById("wx-red").onclick = function() {
            var totalFee = document.getElementById("total-fee").value;
            var openid = document.getElementById("openid").value;
            console.log(totalFee);
            console.log(openid);
            ajaxRequest.open('GET','data/transfer.php?type=wxred&amount=' + totalFee + "&openid=" + openid,true);
            ajaxRequest.setRequestHeader('CONTENT-TYPE','application/x-www-form-urlencoded');
            ajaxRequest.send();
        }

        document.getElementById("wx-transfer").onclick = function() {
            var totalFee = document.getElementById("total-fee").value;
            var openid = document.getElementById("openid").value;
            console.log(totalFee);
            console.log(openid);
            ajaxRequest.open('GET','data/transfer.php?type=wxtransfer&amount=' + totalFee + "&openid=" + openid,true);
            ajaxRequest.setRequestHeader('CONTENT-TYPE','application/x-www-form-urlencoded');
            ajaxRequest.send();
        }
    </script>
</body>
</html>
