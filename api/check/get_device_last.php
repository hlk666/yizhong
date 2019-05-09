<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_GET['device_list'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_list.']);
}

$guardianLast = DbiAdmin::getDbi()->getDeviceLastGuardian($_GET['device_list']);
if (VALUE_DB_ERROR === $guardianLast) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$historyLast = DbiAdmin::getDbi()->getDeviceLastHistory($_GET['hospital_id'], $_GET['device_list']);
if (VALUE_DB_ERROR === $historyLast) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['regist_last'] = $guardianLast;
$result['bind_last'] = $historyLast;
api_exit($result);
