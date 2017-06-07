<?php
require PATH_LIB . 'DbiAnalytics.php';

$patients = DbiAnalytics::getDbi()->getPatientsNeedFollow();
if (VALUE_DB_ERROR === $patients) {
    analytics_exit(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
}


$ret = array();
$ret['code'] = 0;
$ret['patients'] = $patients;

analytics_exit($ret);

function analytics_exit(array $ret)
{
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}
