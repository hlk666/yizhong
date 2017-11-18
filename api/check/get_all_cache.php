<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospitals'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospitals.']);
}

$hospitals = $_GET['hospitals'];
$type = isset($_GET['type']) ? $_GET['type'] : 'diagnosis';
$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;

if ($hospitals == '0') {
    $path = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . $type;
    foreach(scandir($path) as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($path . DIRECTORY_SEPARATOR . $file)) {
            include $path . DIRECTORY_SEPARATOR . $file;
            $hospital = str_replace('.php', '', $file);
            $result[$hospital] = $patients;
        }
    }
} else {
    $hospitalList = explode(',', $hospitals);
    foreach ($hospitalList as $hospital) {
        $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $hospital . '.php';
        if (file_exists($file)) {
            include $file;
            $result[$hospital] = $patients;
        }
    }
}

api_exit($result);
