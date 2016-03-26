<?php
require_once PATH_LIB . 'DataFile.php';
require_once PATH_LIB . 'Logger.php';

if (empty($_GET['device_id'])) {
    echo json_encode(['code' => '1', 'message' => MESSAGE_REQUIRED .'device_id']);
    exit;
}
$deviceId = $_GET['device_id'];
$updateLog = 'app_update.log';

if (strlen($deviceId) >= 2) {
    $city = substr($deviceId, 0, 2);
} else {
    $city = '00';
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;

$dataFile = DataFile::getDataFile('app_update', $city);
if (false === $dataFile) {
    $result['version'] = '0';
} else {
    include $dataFile;
    if (isset($device[$deviceId])) {
        $result['version'] = $device[$deviceId];
        Logger::write($updateLog, 'get update with ID : ' . $deviceId);
    } else { 
        $result['version'] = '0';
    }
}
api_exit($result);