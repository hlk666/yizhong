<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}

if ($_POST['type'] == 'phone_data') {
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'phone_data' . DIRECTORY_SEPARATOR . '1.php';
    unlink($file);
    api_exit_success();
}

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}


clearNotice($_POST['hospital_id'], $_POST['type'], $_POST['patient_id']);

api_exit_success();

