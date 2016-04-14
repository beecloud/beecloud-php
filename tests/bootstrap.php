<?php
require_once("sdk/src/rest/config.php");
if(version_compare(PHP_VERSION, '5.3.0','>')) {
    require_once("sdk/src/network.php");
    require_once("sdk/src/rest/api.php");
    require_once("sdk/src/rest/international.php");
} else {
    require_once("sdk/src/beecloud.php");
}