<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital.']);
}
if (false === Validate::checkRequired($_GET['date'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'date.']);
}

$isAddResult = isset($_GET['add_result']) ? true : false;
$hospital = $_GET['hospital'];
$dateList = array_filter(explode(',', $_GET['date']));
$count = count($dateList);
if ($count < 2) {
    api_exit(['code' => '1', 'message' => MESSAGE_FORMAT . 'date.']);
}

$data = array();
for ($i = 1; $i < $count; $i++) {
    $startTime = $dateList[$i - 1];
    $endTime = $dateList[$i];
    
    $ret = DbiAdmin::getDbi()->getGuardiansTime($hospital, $startTime, $endTime, $isAddResult);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    $data[$startTime] = $ret;
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['data'] = $data;
api_exit($result);
