<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'id.']);
}

$ret = DbiAdmin::getDbi()->delExamQuestion($_POST['id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
