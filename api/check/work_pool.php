<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'id.']);
}

$file = PATH_DATA . 'guardian_on' . DIRECTORY_SEPARATOR . $_GET['id'] . '.txt';
if (!file_exists($file) || empty($file)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

$fileMode = PATH_DATA . 'mode.txt';

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['list'] = file_get_contents($file);
$result['mode'] = file_get_contents($fileMode);

api_exit($result);
