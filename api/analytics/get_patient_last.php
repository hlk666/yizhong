<?php
require PATH_LIB . 'DbiAnalytics.php';

if (empty($_GET['device_id'])) {
    analytics_exit(['code' => '1', 'message' => MESSAGE_REQUIRED .'device_id']);
}

$ret = DbiAnalytics::getDbi()->getPatientLast($_GET['device_id']);
if (VALUE_DB_ERROR === $ret) {
    analytics_exit(['code' => '3', 'message' => 'error']);
}

$ret['code'] = 0;

analytics_exit($ret);

function analytics_exit(array $ret)
{
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}
