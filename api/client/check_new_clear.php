<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['clear_target'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'clear_target.']);
}
$hospitalId = $_POST['hospital_id'];
$clearTarget = $_POST['clear_target'];

$fileEcgNotice = PATH_CACHE_ECG_NOTICE . $hospitalId . '.php';
$fileRegistNotice = PATH_CACHE_REGIST_NOTICE . $hospitalId . '.php';
$fileConsultationApply = PATH_CACHE_CONSULTATION_APPLY_NOTICE . $hospitalId . '.php';
$fileConsultationReply = PATH_CACHE_CONSULTATION_REPLY_NOTICE . $hospitalId . '.php';
$fileUploadData = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'upload_data' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileUploadDataFail = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'upload_data_fail' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileMoveData = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'move_data' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileHbi = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'hbi' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileReport = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'report' . DIRECTORY_SEPARATOR . $hospitalId . '.php';

if ($clearTarget == 'upload_data_fail') {
    unlink($fileUploadDataFail);
}
if ($clearTarget == 'move_data') {
    unlink($fileMoveData);
}

api_exit_success();
