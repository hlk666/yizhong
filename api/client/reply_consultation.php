<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['consultation_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'consultation_id.']);
}
if (false === Validate::checkRequired($_POST['response_message'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'response_message.']);
}

$consultationId = $_POST['consultation_id'];
$responseMessage = $_POST['response_message'];

$ret = Dbi::getDbi()->getConsultationById($consultationId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '1', 'message' => '会诊ID错误。']);
}
$requestHospital = $ret['request_hospital_id'];

$ret = Dbi::getDbi()->flowConsultationReply($consultationId, $responseMessage);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

setNotice($requestHospital, PATH_CACHE_CONSULTATION_REPLY_NOTICE);

api_exit_success();

function setNotice($hospitalId, $directory)
{
    $file = $directory . $hospitalId . '.php';
    if (!file_exists($file)) {
        file_put_contents($file, '1');
    }
}
