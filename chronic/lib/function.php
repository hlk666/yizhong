<?php
function microtime_float()
{
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
}
function before()
{
    $class = str_ireplace(' ', '', ucwords(str_ireplace('_', ' ', $_GET['entry'])));
    $file = PATH_ROOT . 'api' . DIRECTORY_SEPARATOR . $class . '.php';

    if (!file_exists($file)) {
        HpLogger::write($file . ' does not exist.');
        display_404();
    }
    
    include $file;
    return $class;
}

function display_404()
{
    echo PERMISSION_DENY;
    exit;
}

function save_execute_time($startTime)
{
    $time = microtime_float() - $startTime;
    if (!isset($_GET['entry']) || empty($_GET['entry'])) {
        HpLogger::writeDebugTimeLog('site/index', $time);
    } else {
        HpLogger::writeDebugTimeLog($_GET['entry'], $time);
    }
}