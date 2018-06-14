<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital.']);
}
$file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'ecg_data' . DIRECTORY_SEPARATOR . $_GET['hospital'] . '.txt';
if (file_exists($file)) {
    $data = file_get_contents($file);
} else {
    $data = '';
}
unlink($file);

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['data'] = $data;
api_exit($result);
