<?php
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospitals'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospitals.']);
}
$hospitals = $_GET['hospitals'];

$hospitalConfig = DbiAnalytics::getDbi()->getHospitalConfigList($hospitals);
if (VALUE_DB_ERROR === $hospitalConfig) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($hospitalConfig)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $ret['code'] = '0';
    $ret['message'] = MESSAGE_SUCCESS;
    $ret['list'] = $hospitalConfig;
}
api_exit($ret);