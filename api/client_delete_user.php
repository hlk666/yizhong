<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$guardianId = $_GET['patient_id'];

$ret = Dbi::getDbi()->existedGuardian($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (false === $ret) {
    api_exit(['code' => '3', 'message' => MESSAGE_DB_NO_DATA]);
}

$ret = Dbi::getDbi()->flowGuardianDelete($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$result = array();
$result['code'] = '0';
$result['message'] = '';

api_exit($result);
