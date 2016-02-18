<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['mark'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'mark.']);
}
$guardianId = $_POST['patient_id'];
$mark = $_POST['mark'];

$ret = Dbi::getDbi()->markPatient($guardianId, $mark);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();