<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}

$history = DbiAdmin::getDbi()->getHistoryDevice($_GET['device_id']);
if (VALUE_DB_ERROR === $history) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($history)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'history' => $history]);
