<?php
require PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospitals'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospitals.']);
}

$hospitals = $_GET['hospitals'];
$type = isset($_GET['type']) ? $_GET['type'] : 'diagnosis';
$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;

if ($type == 'diagnosis') {
    $patientRecent = array();
    $ret = DbiAnalytics::getDbi()->getRecentpatient();
    if (VALUE_DB_ERROR === $ret) {
        //do nothing.
    } else {
        foreach ($ret as $row) {
            $patientRecent[] = $row['guardian_id'];
        }
    }
}
if ($hospitals == '0') {
    $path = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . $type;
    foreach(scandir($path) as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($path . DIRECTORY_SEPARATOR . $file)) {
            include $path . DIRECTORY_SEPARATOR . $file;
            $hospital = str_replace('.php', '', $file);
            if ($type == 'diagnosis') {
                $newPatients = array_intersect($patientRecent, $patients);
            } else {
                $newPatients = $patients;
            }
            //fix bug.
            $tmp = array();
            foreach ($newPatients as $value) {
                $tmp[] = $value;
            }
            $result[$hospital] = $tmp;
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
