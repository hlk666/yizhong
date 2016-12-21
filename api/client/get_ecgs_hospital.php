<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_GET['start_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'start_time.']);
}
$hospitalId = $_GET['hospital_id'];
$startTime = $_GET['start_time'];


$ret = Dbi::getDbi()->getEcgsHospital($hospitalId, $startTime);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['ecgs'] = $ret;

api_exit($result);

