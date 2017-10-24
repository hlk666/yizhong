<?php
require PATH_LIB . 'DbiAnalytics.php';

if (!isset($_GET['hospital_id']) || empty($_GET['hospital_id'])) {
    analytics_exit(['code' => '1', 'message' => MESSAGE_REQUIRED .'hospital_id']);
}
$hospitalId = $_GET['hospital_id'];
$guardianId = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
$startTime = isset($_GET['start_time']) ? $_GET['start_time'] : null;
$endTime = isset($_GET['end_time']) ? $_GET['end_time'] . ' 23:59:59' : null;

$contents = DbiAnalytics::getDbi()->getTelContent($hospitalId, $guardianId, $startTime, $endTime);
if (VALUE_DB_ERROR === $contents) {
    analytics_exit(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
}

$ret = array();
$ret['code'] = 0;
$ret['contents'] = $contents;

analytics_exit($ret);

function analytics_exit(array $ret)
{
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}
