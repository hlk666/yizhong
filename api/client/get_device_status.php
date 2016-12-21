<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Logger.php';

if (false === Validate::checkRequired($_GET['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}

$deviceIdList = explode(',', $_GET['device_id']);
if (empty($deviceIdList)) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}
$result = ['code' => 0, 'message' => MESSAGE_SUCCESS];
foreach ($deviceIdList as $deviceId) {
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'device_status' . DIRECTORY_SEPARATOR . $deviceId . '.php';
    
    if (file_exists($file)) {
        include $file;
        
        $tmpArray = array();
        
        $tmpArray['phone_power'] = $phone_power;
        $tmpArray['collection_power'] = $collection_power;
        $tmpArray['bluetooth'] = $bluetooth;
        $tmpArray['line'] = $line;
        $result[$deviceId] = $tmpArray;
    } else {
        $result[$deviceId] = array();
    }
}
api_exit($result);