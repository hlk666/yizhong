<?php
require PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$checkText = DbiAnalytics::getDbi()->getCheckText($_GET['patient_id']);
if (VALUE_DB_ERROR === $checkText) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$ret = array();
$ret['code'] = 0;
$ret['checkText'] = $checkText;
echo json_encode($ret, JSON_UNESCAPED_UNICODE);
exit;
