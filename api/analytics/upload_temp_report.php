<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_GET['size'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'size.']);
}

$guardianId = $_GET['patient_id'];
$data = file_get_contents('php://input');
if (empty($data)) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED]);
}

if (strlen($data) != $_GET['size']) {
    api_exit(['code' => '2', 'message' => $guardianId . ' : Data size is wrong.']);
}

$dir = PATH_ROOT . 'report' . DIRECTORY_SEPARATOR;
if (!file_exists($dir)) {
    mkdir($dir);
}
$file = $dir . $guardianId . '.pdf';

$ret = file_put_contents($file, $data);
if (false === $ret) {
    api_exit(['code' => '5', 'message' => $guardianId . 'IO error.']);
}

api_exit_success();
