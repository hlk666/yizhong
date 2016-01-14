<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
$hospitalId = $_GET['hospital_id'];

$fileEcgNotice = PATH_CACHE_ECG_NOTICE . $hospitalId . '.php';
$fileRegistNotice = PATH_CACHE_REGIST_NOTICE . $hospitalId . '.php';

if (!file_exists($fileEcgNotice) && !file_exists($fileRegistNotice)) {
    api_exit(['code' => '3', 'message' => MESSAGE_DB_NO_DATA]);
}

$result = array();
$result['code'] = '0';
$result['message'] = '';
$result['patients'] = array();
$result['new_patient'] = '0';

if (file_exists($fileEcgNotice)) {
    include $fileEcgNotice;
    $result['patients'] = $patients;
    unlink($fileEcgNotice);
}

if (file_exists($fileRegistNotice)) {
    include $fileRegistNotice;
    $result['new_patient'] = '1';
    unlink($fileRegistNotice);
}

api_exit($result);
