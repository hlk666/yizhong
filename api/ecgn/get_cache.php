<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['department_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'department_id.']);
}
if (false === Validate::checkRequired($_GET['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;

$file = PATH_ROOT . 'ecgn_cache' . DIRECTORY_SEPARATOR . $_GET['type'] . DIRECTORY_SEPARATOR . $_GET['department_id'] . '.txt';
if (!file_exists($file)) {
    $cache = '';
} else {
    $cache = file_get_contents($file);
}
$result['cache'] = $cache;
api_exit($result);
