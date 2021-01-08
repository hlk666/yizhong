<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

$hospitalId = isset($_GET['hospital_id']) && !empty($_GET['hospital_id']) ? $_GET['hospital_id'] : null;
$agencyId = isset($_GET['agency_id']) && !empty($_GET['agency_id']) ? $_GET['agency_id'] : null;
$userId = isset($_GET['user_id']) && !empty($_GET['user_id']) ? $_GET['user_id'] : null;
$startTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$endTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] : null;
$status = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : null;

$planRet = DbiSale::getDbi()->getPlan($hospitalId, $agencyId, $userId, $startTime, $endTime, $status);
if (VALUE_DB_ERROR === $planRet) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
$planIdList = '';
foreach ($planRet as $row) {
    $planIdList .= $row['plan_id'] . ',';
}
$planIdList = substr($planIdList, 0, -1);

//1.plan数据
//2.record数据，用planID查询的
//3.record数据，用time查询的

$recordRet1 = DbiSale::getDbi()->getRecord(null, null, null, null, null, $planIdList);
if (VALUE_DB_ERROR === $recordRet1) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$recordRet2 = DbiSale::getDbi()->getRecord($hospitalId, $agencyId, $userId, $startTime, $endTime, null);
if (VALUE_DB_ERROR === $recordRet2) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}


$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['plan'] = $planRet;
$result['record1'] = $recordRet1;
$result['record2'] = $recordRet2;
api_exit($result);
