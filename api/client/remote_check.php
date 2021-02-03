<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Invigilator.php';
require_once PATH_LIB . 'Mqtt.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$guardianId = $_POST['patient_id'];
$data = array('check_info' => 'on');

$invigilator = new Invigilator($guardianId);
$ret = $invigilator->create($data);

if (VALUE_PARAM_ERROR === $ret) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (VALUE_GT_ERROR === $ret) {
    api_exit(['code' => '3', 'message' => 'App离线。']);
}
api_exit_success();
