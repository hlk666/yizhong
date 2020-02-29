<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_list'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_list.']);
}

$list = DbiAdmin::getDbi()->getEcgLast($_GET['patient_list']);
if (VALUE_DB_ERROR === $list) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($list)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['ecgs'] = $list;

api_exit($result);
