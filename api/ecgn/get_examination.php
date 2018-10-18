<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';


if (false === Validate::checkRequired($_GET['status'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'status.']);
}


$name = isset($_GET['name']) ? $_GET['name'] : null;
//$departmentId = isset($_GET['department_id']) ? $_GET['department_id'] : null;
//$departmentId = isset($_GET['department_id']) ? $_GET['department_id'] : null;
//$departmentId = isset($_GET['department_id']) ? $_GET['department_id'] : null;
$caseId = isset($_GET['case_id']) ? $_GET['case_id'] : null;
$hospitalizationId = isset($_GET['hospitalization_id']) ? $_GET['hospitalization_id'] : null;
$outpatientId = isset($_GET['outpatient_id']) ? $_GET['outpatient_id'] : null;
$medicalInsuranceId = isset($_GET['medical_insurance']) ? $_GET['medical_insurance'] : null;
$roomId = isset($_GET['room_id']) ? $_GET['room_id'] : null;

$applyStartTime = isset($_GET['apply_start_time']) ? $_GET['apply_start_time'] : null;
$applyEndTime = isset($_GET['apply_end_time']) ? $_GET['apply_end_time'] : null;
$orderStartTime = isset($_GET['order_start_time']) ? $_GET['order_start_time'] : null;
$orderEndTime = isset($_GET['order_end_time']) ? $_GET['order_end_time'] : null;


$ret = DbiEcgn::getDbi()->getExamination($_GET['status'], $name, 
        $caseId, $hospitalizationId, $outpatientId, $medicalInsuranceId, $roomId, 
        $applyStartTime, $applyEndTime, $orderStartTime, $orderEndTime);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['list'] = $ret;
    api_exit($result);
}
