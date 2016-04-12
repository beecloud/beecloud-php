<?php

$BCRESTPath = dirname(__FILE__);
set_include_path(get_include_path().PATH_SEPARATOR.$BCRESTPath);
$BCPHPVersion=phpversion();
$BCNEEDVersion='5.3.0';
if(version_compare($BCPHPVersion,$BCNEEDVersion,'>')) {
    require_once("sdk/src/network.php");
    require_once("sdk/src/rest/api.php");
    require_once("sdk/src/rest/international.php");
    $api = new \beecloud\rest\api();
    $international = new \beecloud\rest\international();
} else {
    require_once("sdk/src/beecloud.php");
    $api = new BCRESTApi();
    $international = new BCRESTInternational();
}
