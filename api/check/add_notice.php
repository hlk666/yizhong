<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['notice'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'notice.']);
}

$guardianId = $_POST['patient_id'];
$notice = $_POST['notice'];

$ret = DbiAdmin::getDbi()->addNotice($guardianId, $notice);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}


api_exit_success();
