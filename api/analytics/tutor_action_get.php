<?php
require PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['tutor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tutor_id.']);
}
if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$ret = DbiAnalytics::getDbi()->getTutorAction($_GET['tutor_id'], $_GET['patient_id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['list'] = $ret;
api_exit($result);
