<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Invigilator.php';
require_once PATH_LIB . 'function.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$guardianId = $_POST['patient_id'];
$data = array('action' => 'end');

$invigilator = new Invigilator($guardianId);
$ret = $invigilator->create($data);

if (VALUE_PARAM_ERROR === $ret) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$path = PATH_DATA . 'guardian_on' . DIRECTORY_SEPARATOR;
$fileList = scandir($path);
foreach ($fileList as $f) {
    if ($f != '.' && $f != '..') {
        $text = file_get_contents($path . $f);
        if (strstr($text, $guardianId) !== false) {
            refreshCacheFile(false, $path . $f, ',', $guardianId);
        }
    }
}

if (VALUE_GT_ERROR === $ret) {
    api_exit(['code' => '3', 'message' => MESSAGE_GT_ERROR]);
}

api_exit_success();
