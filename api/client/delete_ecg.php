<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['ecg_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'ecg_id.']);
}

$ecgId = $_POST['ecg_id'];

$ret = Dbi::getDbi()->existedEcg($ecgId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (false === $ret) {
    api_exit(['code' => '18', 'message' => '该删除对象不存在。']);
}

$ret = Dbi::getDbi()->delEcg($ecgId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
