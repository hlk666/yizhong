<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$hospitalId = $_GET['hospital_id'];
$guardianId = $_GET['patient_id'];
//$file = PATH_ROOT . 'report' . DIRECTORY_SEPARATOR . $hospitalId . DIRECTORY_SEPARATOR . $guardianId . '.pdf';
$file = PATH_ROOT . 'report' . DIRECTORY_SEPARATOR . $guardianId . '.pdf';
if (!file_exists($file)) {
    api_exit(['code' => '24', 'message' => '报告文件不存在，请确认传输了正确的监护医院和病人ID。']);
}

//$url = URL_ROOT . 'report/' . $hospitalId . '/' . $guardianId . '.pdf';
$url = URL_ROOT . 'report/' . $guardianId . '.pdf';
clearNotice($hospitalId, 'report', $guardianId);

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['url'] = $url;
api_exit($result);