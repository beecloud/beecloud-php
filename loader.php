<?php
/**
 * Created by PhpStorm.
 * User: dengze
 * Date: 9/16/15
 * Time: 13:16
 */
$BCRESTPath = dirname(__FILE__);
set_include_path(get_include_path().PATH_SEPARATOR.$BCRESTPath);
$BCPHPVersion=phpversion();
$BCNEEDVersion='5.3.0';
if(version_compare($BCPHPVersion,$BCNEEDVersion,'>')) {
    require_once("sdk/src/network.php");
    require_once("sdk/src/rest/api.php");
    require_once("sdk/src/rest/international.php");
} else {
    require_once("degrade/beecloud.php");
}

