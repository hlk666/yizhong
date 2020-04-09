<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}
if (false === Validate::checkRequired($_POST['file_size'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'file_size.']);
}
if (false === Validate::checkRequired($_POST['left_size'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'left_size.']);
}
if (false === Validate::checkRequired($_POST['total_size'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'total_size.']);
}

if ($_POST['file_size'] < '0.3') {
    setNotice('1', 'data_size', $_POST['patient_id']);
}

$ret = Dbi::getDbi()->saveFileSize($_POST['patient_id'], $_POST['device_id'], 
        $_POST['file_size'], $_POST['left_size'], $_POST['total_size']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();