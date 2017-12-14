<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
$hospital = $_GET['hospital_id'];

$accountList = DbiAdmin::getDbi()->getAccountList($hospital);
if (VALUE_DB_ERROR === $accountList) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($accountList)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'doctors' => $accountList]);
