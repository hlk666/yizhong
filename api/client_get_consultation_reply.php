<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
$hospitalId = $_GET['hospital_id'];

$ret = Dbi::getDbi()->getConsultationResponse($hospitalId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '3', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $idList = '(0';
    foreach ($ret as $consultation) {
        $idList .= ',' . $consultation['consultation_id'];
    }
    $idList .= ')';
    $endConsultation = Dbi::getDbi()->flowConsultationEnd($idList);
    if (VALUE_DB_ERROR === $endConsultation) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    
    $result = array();
    $result['code'] = '0';
    $result['message'] = '';

    $result['reply_consultation'] = $ret;
    api_exit($result);
}
