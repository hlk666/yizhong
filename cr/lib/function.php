<?php
function microtime_float()
{
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
}
function before_api()
{
    if (!isset($_GET['entry']) || empty($_GET['entry'])) {
        echo PERMISSION_DENY;
        exit;
    }
    
    $route = explode('_', $_GET['entry'], 2);
    if (count($route) == 2 && !empty($route[1])) {
        $category = $route[0];
        $class = str_ireplace(' ', '', ucwords(str_ireplace('_', ' ', $route[1])));
        $file = PATH_ROOT . 'logic' . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . $class . '.php';
    } else {
        $file = '';
    }
    if ('' == $file || !file_exists($file)) {
        HpLogger::writeCommonLog($file . ' does not exist.');
        echo PERMISSION_DENY;
        exit;
    }
    
    include $file;
    return $class;
}
function after_api(array $returnArray, $time)
{
    if (empty($returnArray)) {
        $returnArray = ['code' => '99', 'message' => '发生未知错误，请联系管理员。'];
    }
    echo json_encode($returnArray, JSON_UNESCAPED_UNICODE);

    HpLogger::writeDebugLog($_GET['entry'], $time);
    
    exit;
}
