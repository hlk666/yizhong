<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'HpVerificationCode.php';

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['key'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'key.']);
}

$key = HpVerificationCode::getVC($_POST['hospital_id']);
if (empty($key) || $key != $_POST['key']) {
    api_exit(['code' => '4', 'message' => '验证码错误。']);
}

$vcFile = PATH_ROOT . 'vc' . DIRECTORY_SEPARATOR . $_POST['hospital_id'] . '.php';
if (file_exists($vcFile)) {
    unlink($vcFile);
}
api_exit(['code' => '0', 'message' => '验证码正确。']);
