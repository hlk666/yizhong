<?php
require PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}
if (false === Validate::checkRequired($_POST['level'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'level.']);
}
if (false === Validate::checkRequired($_POST['url'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'url.']);
}

$id = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;

$ret = DbiAdmin::getDbi()->addExamQuestion($id, $_POST['type'], $_POST['level'], $_POST['url']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
