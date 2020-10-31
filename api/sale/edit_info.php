<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'id.']);
}
if (false === Validate::checkRequired($_POST['table'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'table.']);
}

$data = $_POST;
unset($data['id']);
unset($data['table']);

if (empty($data)) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}

$isExisted = DbiSale::getDbi()->existedId($_POST['table'], $_POST['id']);
if (VALUE_DB_ERROR === $isExisted) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (true !== $isExisted) {
    api_exit(['code' => '3', 'message' => '该数据不存在。']);
}

$ret = DbiSale::getDbi()->editById($_POST['table'], $_POST['id'], $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
