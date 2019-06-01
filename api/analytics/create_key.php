<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'HpVerificationCode.php';

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}

$key = HpVerificationCode::createFileNumericVC($_POST['hospital_id']);
if (empty($key)) {
    api_exit(['code' => '2', 'message' => '验证码创建失败，请重试或者联系管理员。']);
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'key' => $key]);
