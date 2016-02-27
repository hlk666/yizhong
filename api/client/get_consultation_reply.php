<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
$hospitalId = $_GET['hospital_id'];
$allFlag = isset($_GET['all_flag']) ? $_GET['all_flag'] : 0;

$ret = Dbi::getDbi()->getConsultationResponse($hospitalId, $allFlag);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    //close the consultation.
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
    $result['message'] = MESSAGE_SUCCESS;
    $result['reply_consultation'] = $ret;
    api_exit($result);
}
