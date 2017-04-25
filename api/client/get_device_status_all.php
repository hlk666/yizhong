<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'Dbi.php';

$guardians = Dbi::getDbi()->getGuardians(0, 0, 10000, null, 1);
if (VALUE_DB_ERROR === $guardians) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($guardians)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

$data = array();
foreach ($guardians as $guardian) {
    $deviceId = $guardian['device_id'];
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'device_status' . DIRECTORY_SEPARATOR . $deviceId . '.php';
    if (file_exists($file)) {
        include $file;
        $tmpArray = array();
        $tmpArray['device_id'] = $deviceId;
        $tmpArray['phone_power'] = $phone_power;
        $tmpArray['collection_power'] = $collection_power;
        $tmpArray['bluetooth'] = $bluetooth;
        $tmpArray['line'] = $line;
        $tmpArray['time'] = isset($time) ? $time : '';
        $data[] = $tmpArray;
    }
}

$result = ['code' => 0, 'message' => MESSAGE_SUCCESS, 'status' => $data];
api_exit($result);