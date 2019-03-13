<?php
require PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['text'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'text.']);
}

$ret = DbiAdmin::getDbi()->addProblem($_POST['patient_id'], $_POST['text']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'id' => $ret, 'patient_id' => $_POST['patient_id']]);
