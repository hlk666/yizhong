<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['room_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'room_id.']);
}
if (false === Validate::checkRequired($_POST['patient_list'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_list.']);
}

$dir = PATH_ROOT . 'ecgn' . DIRECTORY_SEPARATOR . 'room';
if (!file_exists($dir)) {
    mkdir($dir);
}
$file = $dir . DIRECTORY_SEPARATOR . $_GET['room_id'] . '.txt';
file_put_contents($file, $_POST['patient_list']);

api_exit_success();
