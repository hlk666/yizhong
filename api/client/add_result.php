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
    api_exit(['code' => '11', 'message' => '该医生账户不存在。']);
}
if ($ret['password'] != $pwd) {
    api_exit(['code' => '12', 'message' => '密码错误。']);
}

$ret = Dbi::getDbi()->getGuardianById($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '14', 'message' => '找不到该病人的监护信息。']);
}
if (($ret['mode'] == 1 || $ret['mode'] == 2) && $ret['status'] < 2) {
    api_exit(['code' => '15', 'message' => '病人尚未结束监护，请结束监护后再做出病情 。']);
}

$ret = Dbi::getDbi()->flowGuardianAddResult($guardianId, $result);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
