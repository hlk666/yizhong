<?php
require_once PATH_LIB . 'DbiWX.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}

$isReportHospital = DbiWX::getDbi()->checkHospitalType($_GET['hospital_id']);
if ($isReportHospital === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (true == $isReportHospital) {
    $reportFlag = '1';
} else {
    $reportFlag = '0';
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['report_flag'] = $reportFlag;
api_exit($result);
