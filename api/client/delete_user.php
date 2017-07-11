<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$guardianId = $_POST['patient_id'];

$ret = Dbi::getDbi()->existedGuardian($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (false === $ret) {
    api_exit(['code' => '18', 'message' => '该删除对象不存在。']);
}

$file = PATH_ROOT . 'report' . DIRECTORY_SEPARATOR . $guardianId . '.pdf';
if (file_exists($file)) {
    api_exit(['code' => '25', 'message' => '已出报告的监护信息不能删除，请联系管理员。']);
}

$ret = Dbi::getDbi()->flowGuardianDelete($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
