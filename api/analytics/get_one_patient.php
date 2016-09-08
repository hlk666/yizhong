<?php
require PATH_LIB . 'DbiAnalytics.php';

if (empty($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (empty($_GET['verification_code'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'verification_code.']);
}

$guardianId = $_GET['patient_id'];
$vcCode = $_GET['verification_code'];
if (!is_numeric($guardianId)) {
    api_exit(['code' => '1', 'message' => MESSAGE_FORMAT . 'patient_id.']);
}

$vcFile = PATH_ROOT . 'VerificationCode' . DIRECTORY_SEPARATOR . $guardianId . '.php';
$vcMessage = "验证码错误。";
if (!file_exists($vcFile)) {
    api_exit(['code' => '23', 'message' => $vcMessage]);
}
include $vcFile;
if ($vcCode != $rightVC) {
    api_exit(['code' => '23', 'message' => $vcMessage]);
}

$patient = DbiAnalytics::getDbi()->getPatient($guardianId);
if (VALUE_DB_ERROR === $patient) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$patient['code'] = 0;
$patient['age'] = date('Y') - $patient['birth_year'];
$patient['sex'] = $row['sex'] == 1 ? '男' : '女';
unset($patient['birth_year']);

api_exit($patient);
