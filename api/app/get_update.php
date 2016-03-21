<?php
require_once PATH_LIB . 'DataFile.php';
require_once PATH_LIB . 'Logger.php';

if (empty($_GET['device_id'])) {
    echo json_encode(['code' => '1', 'message' => MESSAGE_REQUIRED .'device_id']);
    exit;
}
$deviceId = $_GET['device_id'];

if (strlen($deviceId) == 5) {
    $city = substr($deviceId, 0, 3);
} elseif (strlen($deviceId) == 8) {
    $city = substr($deviceId, 0, 4);
} else {
    echo json_encode(['code' => '1', 'message' => MESSAGE_PARAM]);
    exit;
}

$dataFile = DataFile::getDataFile('app_update', $city);
if (false === $dataFile) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
    exit;
}
include $dataFile;

foreach ($device as $key => $value) {
    if ($value == $deviceId) {
        unset($device[$key]);
        Logger::write($updateLog, 'get update with ID : ' . $deviceId);
        DataFile::setDataFile('app_update', $city, ['device' => $device]);
        
        $result = array();
        $result['code'] = '0';
        $result['message'] = MESSAGE_SUCCESS;
        $result['update_flag'] = '1';
        api_exit($result);
    }
}

api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
exit;