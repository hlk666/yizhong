<?php
header("Content-Type:text/html;charset=utf-8");

require 'config/config.php';
require_once PATH_ROOT . 'lib/util/HpLogger.php';
require_once PATH_ROOT . 'lib/function.php';
require_once PATH_ROOT . 'config/HpErrorMessage.php';

error_reporting(E_ALL);

$startTime = microtime_float();

$class = before($_GET);
$obj = new $class();
$obj->run();

save_execute_time($startTime);

exit;
