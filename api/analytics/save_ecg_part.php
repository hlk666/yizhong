<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

if (false === Validate::checkRequired($_POST['title'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'title.']);
}

if (false === Validate::checkRequired($_POST['data'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'data.']);
}
$patientId = $_GET['patient_id'];
$title = mb_convert_encoding($_POST['title'], 'GBK', 'UTF-8');
$data = $_POST['data'];

$path = PATH_LONG_RANGE . $patientId . DIRECTORY_SEPARATOR;
if (!file_exists($path)) {
    mkdir($path);
}

$file = $path . $title . date('YmdHis') . '.txt';
if (file_exists($file)) {
    unlink($file);
}

$ret = file_put_contents($file, $data);
if (false === $ret) {
    api_exit(['code' => '4', 'message' => 'Server IO error.']);
}

api_exit_success();
