<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['ecg_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'ecg_id.']);
}
if (false === Validate::checkRequired($_POST['mark'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'mark.']);
}
$ecgId = $_POST['ecg_id'];
$mark = $_POST['mark'];

$ret = Dbi::getDbi()->markEcg($ecgId, $mark);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$result = array();
$result['code'] = '0';
$result['message'] = '';

api_exit($result);