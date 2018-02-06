<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['start_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'start_time.']);
}
/*
if (false === Validate::checkRequired($_GET['end_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'end_time.']);
}
*/
if ($_GET['end_time'] >= date('Y-m-d')) {
    $endTime =  date('Y-m-d', strtotime('-1 day'));
} else {
    $endTime = $_GET['end_time'];
}
$startTime = str_replace('-', '', substr($_GET['start_time'], 0, 10));
$endTime = str_replace('-', '', substr($endTime, 0, 10));


$startFile = PATH_DATA . 'device_count' . DIRECTORY_SEPARATOR . $startTime . '.php';
if (file_exists($startFile)) {
    include $startFile;
} else {
    api_exit(['code' => '1', 'message' => '缓存文件不足.']);
}
$startDeviceInfo = $deviceInfo;
$endFile = PATH_DATA . 'device_count' . DIRECTORY_SEPARATOR . $endTime . '.php';
if (file_exists($endFile)) {
    include $endFile;
}
else {
    api_exit(['code' => '1', 'message' => '缓存文件不足.']);
}
$endDeviceInfo = $deviceInfo;

$hospitals = array_unique(array_merge(array_keys($startDeviceInfo), array_keys($endDeviceInfo)));
$data = array();
foreach ($hospitals as $hospital) {
    $startCount = isset($startDeviceInfo[$hospital]) ? $startDeviceInfo[$hospital] : 0;
    $endCount = isset($endDeviceInfo[$hospital]) ? $endDeviceInfo[$hospital] : 0;
    //$data[$hospital] = $endCount - $startCount;
    $tmp = ['name' => $hospital, 'count' => $endCount - $startCount];
    $data[] = $tmp;
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['device_change'] = $data;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;
