<?php
require PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}
if (false === Validate::checkRequired($_POST['content'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'content.']);
}
if (false === Validate::checkRequired($_POST['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}
if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$deviceId = isset($_POST['device_id']) ? $_POST['device_id'] : '0';


$ret = DbiAdmin::getDbi()->addCommunication($_POST['hospital_id'], 
        $_POST['type'], $_POST['content'], $_POST['user'], $deviceId, $_POST['patient_id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
