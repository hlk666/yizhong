<?php
header("Content-Type:text/html;charset=utf-8");

require '../config/config.php';
require_once PATH_ROOT . 'lib/util/HpLogger.php';
require_once PATH_ROOT . 'lib/function.php';
require_once PATH_ROOT . 'config/HpErrorMessage.php';
require_once PATH_ROOT . 'config/HpAuthority.php';

$class = before_api($_GET);

$startTime = microtime_float();

$obj = new $class();
$retArray = $obj->run();

$time = microtime_float() - $startTime;
after_api($retArray, $time);
