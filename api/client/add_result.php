<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['doctor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id.']);
}
if (false === Validate::checkRequired($_POST['password'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'password.']);
}
if (false === Validate::checkRequired($_POST['result'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'result.']);
}
$guardianId = $_POST['patient_id'];
$ecgId = $_POST['ecg_id'];
$doctorId = $_POST['doctor_id'];
$result = $_POST['result'];

$pwd = md5($_POST['password']);
$ret = Dbi::getDbi()->getAcountById($doctorId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '3', 'message' => '该医生不存在。']);
}
if ($ret['password'] != $pwd) {
    api_exit(['code' => '3', 'message' => '密码有误。']);
}

$ret = Dbi::getDbi()->getGuardianById($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => '该监护不存在。']);
}
if (($ret['mode'] == 1 || $ret['mode'] == 2) && $ret['status'] < 2) {
    api_exit(['code' => '5', 'message' => '该用户尚未结束监护，如需下诊断总结，请将该用户结束监护。']);
}

$ret = Dbi::getDbi()->flowGuardianAddResult($guardianId, $result);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$result = array();
$result['code'] = '0';
$result['message'] = '';

api_exit($result);
