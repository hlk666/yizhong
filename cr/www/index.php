<?php
header("Content-Type:text/html;charset=utf-8");

require '../config/config.php';
require_once PATH_ROOT . 'lib/util/HpLogger.php';
require_once PATH_ROOT . 'lib/function.php';
require_once PATH_ROOT . 'config/HpErrorMessage.php';
require_once PATH_ROOT . 'config/HpAuthority.php';

$class = before_api($_GET);

$startTime = microtime_float();

if (class_exists($class)) {
    $obj = new $class();
    $retArray = $obj->run();
} else {
    HpLogger::writeCommonLog("class[$class] not deined.");
    $retArray = HpErrorMessage::getError(ERROR_OTHER);
}


$time = microtime_float() - $startTime;
after_api($retArray, $time);
