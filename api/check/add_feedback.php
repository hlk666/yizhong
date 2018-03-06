<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['feedback'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'feedback.']);
}

$ret = DbiAdmin::getDbi()->addDeviceFeedback($_POST['hospital_id'], $_POST['feedback']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
