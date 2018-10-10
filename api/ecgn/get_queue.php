<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['room_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'room_id.']);
}
if (false === Validate::checkRequired($_GET['patient_list'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_list.']);
}

$dir = PATH_ROOT . 'ecgn' . DIRECTORY_SEPARATOR . 'room';
if (!file_exists($dir)) {
    mkdir($dir);
}
$file = $dir . DIRECTORY_SEPARATOR . $_GET['room_id'] . '.txt';
$ret = file_get_contents($file);

api_exit(['code' => '0', 'message' => '', 'queue' => $ret]);
