<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'ShortMessageService.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}

$hospitalId = $_GET['hospital_id'];
$guardianId = $_POST['patient_id'];
$ret = Dbi::getDbi()->getDownloadData($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($ret) || empty($ret['download_start_time'])) {
    api_exit(['code' => '20', 'message' => '该用户文件尚未开始下载。']);
}

$type = $_POST['type'];
$data = array();

if ($type == '1') {
    $data['download_end_time'] =  date('Y-m-d H:i:s');
} elseif ($type == '2'){
    $data['download_start_time'] = 'null';
    $data['download_end_time'] = 'null';
} else {
    api_exit(['code' => '2', 'message' => MESSAGE_PARAM]);
    setNotice($hospitalId, 'upload_data', $guardianId);
}

$ret = Dbi::getDbi()->noticeDownloadData($guardianId, $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
