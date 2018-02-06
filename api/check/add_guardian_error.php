<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['content'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'content.']);
}

$guardianId = $_POST['patient_id'];
$content = $_POST['content'];

$ret = Dbi::getDbi()->addGuardError($guardianId, $content);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$data = array();
$data['download_start_time'] = 'null';
$data['download_end_time'] = 'null';
$data['status'] = 7;
$data['download_doctor'] = 0;
$ret = Dbi::getDbi()->noticeDownloadData($guardianId, $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
