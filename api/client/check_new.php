<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
$hospitalId = $_GET['hospital_id'];

$fileEcgNotice = PATH_CACHE_ECG_NOTICE . $hospitalId . '.php';
$fileRegistNotice = PATH_CACHE_REGIST_NOTICE . $hospitalId . '.php';
$fileConsultationApply = PATH_CACHE_CONSULTATION_APPLY_NOTICE . $hospitalId . '.php';
$fileConsultationReply = PATH_CACHE_CONSULTATION_REPLY_NOTICE . $hospitalId . '.php';
$fileUploadData = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'upload_data' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileHbi = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'hbi' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileReport = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'report' . DIRECTORY_SEPARATOR . $hospitalId . '.php';

if (!file_exists($fileEcgNotice) 
        && !file_exists($fileRegistNotice) 
        && !file_exists($fileConsultationApply) 
        && !file_exists($fileConsultationReply) 
        && !file_exists($fileUploadData)
        && !file_exists($fileHbi)
        && !file_exists($fileReport)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['patients'] = array();
$result['new_patient'] = '0';
$result['new_consultation_apply'] = '0';
$result['new_consultation_reply'] = '0';
$result['upload_data'] = array();
$result['hbi'] = array();
$result['report'] = array();

if (file_exists($fileEcgNotice)) {
    include $fileEcgNotice;
    $result['patients'] = $patients;
    unlink($fileEcgNotice);
}

if (file_exists($fileRegistNotice)) {
    $result['new_patient'] = file_get_contents($fileRegistNotice);
    unlink($fileRegistNotice);
}

if (file_exists($fileConsultationApply)) {
    $result['new_consultation_apply'] = '1';
    unlink($fileConsultationApply);
}

if (file_exists($fileConsultationReply)) {
    $result['new_consultation_reply'] = '1';
    unlink($fileConsultationReply);
}

if (file_exists($fileUploadData)) {
    include $fileUploadData;
    $result['upload_data'] = $patients;
}

if (file_exists($fileHbi)) {
    include $fileHbi;
    $result['hbi'] = $patients;
    unlink($fileHbi);
}

if (file_exists($fileReport)) {
    include $fileReport;
    $result['report'] = $patients;
    unlink($fileReport);
}

api_exit($result);
