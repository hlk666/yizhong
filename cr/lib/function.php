<?php
function microtime_float()
{
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
}
function before()
{
    if (!isset($_GET['entry']) || empty($_GET['entry'])) {
        $category = 'site';
        $page = 'index';
    } else {
        $route = explode('/', $_GET['entry'], 2);
        if (count($route) == 2) {
            $category = $route[0];
            $page = $route[1];
        }
        if (count($route) == 1) {
            $category = 'site';
            $page = $route[0];
        }
    }
    $page = str_replace('/', '_', $page);
    
    $class = str_ireplace(' ', '', ucwords(str_ireplace('_', ' ', $page)));
    $file = PATH_ROOT . 'logic' . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . $class . '.php';

    if (!file_exists($file)) {
        HpLogger::writeCommonLog($file . ' does not exist.');
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