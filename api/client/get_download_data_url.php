<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (empty($_GET['verification_code'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'verification_code.']);
}

$guardianId = $_GET['patient_id'];
$vcCode = $_GET['verification_code'];
$vcFile = PATH_ROOT . 'VerificationCode' . DIRECTORY_SEPARATOR . $guardianId . '.php';
$vcMessage = "验证码错误。";
if (!file_exists($vcFile)) {
    api_exit(['code' => '23', 'message' => $vcMessage]);
}
include $vcFile;
if ($vcCode != $rightVC) {
    api_exit(['code' => '23', 'message' => $vcMessage]);
}


$ret = Dbi::getDbi()->getDownloadData($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

if (!empty($ret['download_start_time'])) {
    if (empty($ret['download_end_time'])) {
        api_exit(['code' => '21', 'message' => '其他分析人员正在下载，请稍后再试。']);
    } else {
        api_exit(['code' => '22', 'message' => '其他分析人员已接单，请选择其他用户文件。']);
    }
}

$url = $ret['url'];
$data = ['download_start_time' => date('Y-m-d H:i:s')];
$ret = Dbi::getDbi()->noticeDownloadData($guardianId, $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['url'] = $url;
api_exit($result);



