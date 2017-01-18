<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_ROOT . 'lib/tool/HpMessage.php';
require_once PATH_LIB . 'GeTuiECGOnline.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['ecg_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'ecg_id.']);
}
if (false === Validate::checkRequired($_POST['request_hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'request_hospital_id.']);
}
if (false === Validate::checkRequired($_POST['request_message'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'request_message.']);
}
if (false === Validate::checkRequired($_POST['response_hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'response_hospital_id.']);
}

$guardianId = $_POST['patient_id'];
$ecgId = $_POST['ecg_id'];
$requestHospital = $_POST['request_hospital_id'];
$mesage = $_POST['request_message'];
$responseHospital = $_POST['response_hospital_id'];

$ret = Dbi::getDbi()->flowConsultationApply($guardianId, $requestHospital, $responseHospital, $ecgId, $mesage);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

setConsultationNotice($responseHospital, PATH_CACHE_CONSULTATION_APPLY_NOTICE);
HpMessage::sendTelMessage('有新的会诊请求，请确认。', $responseHospital);

GTSendMessage($responseHospital);

api_exit_success();

function GTSendMessage($hospitalId)
{
    $file = PATH_CACHE_ECGONLINE . $hospitalId . '.php';
    if (file_exists($file)) {
        include $file;
        GeTuiECGOnline::pushToList($clientIdList, '有新的会诊请求，请确认。');
    } else {
        //do nothing.
    }
}
