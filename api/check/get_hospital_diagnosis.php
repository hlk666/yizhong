<?php
require_once PATH_LIB . 'DbiAdmin.php';

$level = isset($_GET['level']) && !empty($_GET['level']) ? $_GET['level'] : null;
$reportHospital = isset($_GET['report_hospital']) && !empty($_GET['report_hospital']) ? $_GET['report_hospital'] : null;
$agency = isset($_GET['agency']) && !empty($_GET['agency']) ? $_GET['agency'] : null;
$salesman = isset($_GET['salesman']) && !empty($_GET['salesman']) ? $_GET['salesman'] : null;
$sTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$eTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] : null;

$ret = DbiAdmin::getDbi()->getHospitalDiagnosis($level, $reportHospital, $agency, $salesman);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['hospitals'] = $ret;
    api_exit($result);
}
