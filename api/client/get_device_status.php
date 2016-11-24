<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Logger.php';

if (false === Validate::checkRequired($_GET['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}

$deviceId = $_GET['device_id'];
$file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'device_status' . DIRECTORY_SEPARATOR . $deviceId . '.php';

if (file_exists($file)) {
    include $file;
    
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    
    $result['phone_power'] = $phone_power;
    $result['collection_power'] = $collection_power;
    $result['bluetooth'] = $bluetooth;
    $result['line'] = $line;
    
    api_exit($result);
} else {
    Logger::writeCommonError('cache file not existed with ID:' . $deviceId);
    api_exit(['code' => '19', 'message' => '无法查看设备状态。']);
}
