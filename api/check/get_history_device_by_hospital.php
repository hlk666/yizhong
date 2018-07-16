<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospitals'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospitals.']);
}
if (false === Validate::checkRequired($_GET['start_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'start_time.']);
}
if (false === Validate::checkRequired($_GET['end_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'end_time.']);
}

$history = DbiAdmin::getDbi()->getHistoryDeviceByHospital($_GET['hospitals'], $_GET['start_time'], $_GET['end_time']);
if (VALUE_DB_ERROR === $history) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($history)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'history' => $history]);
