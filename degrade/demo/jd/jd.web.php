<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud京东网页支付</title>
</head>
<body>
<?php
require_once("../../beecloud.php");

$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["channel"] = "JD_WEB";
$data["total_fee"] = 1;
$data["bill_no"] = "bcdemo" . $data["timestamp"];
$data["title"] = "白开水";
$data["return_url"] = "http://payservice.beecloud.cn";

//选填 optional
$data["optional"] = json_decode(json_encode(array("tag"=>"msgtoreturn")));
//选填 show_url
//$data["show_url"] = "";

try {
    $result = BCRESTApi::bill($data);
    if ($result->result_code != 0) {
        echo json_encode($result);
        exit();
    }

    $htmlContent = $result->html;
    $url = $result->url;
    ?>
    <script>
        window.location.href = "<?php echo $url;?>";
    </script>
<?php
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</body>
</html>