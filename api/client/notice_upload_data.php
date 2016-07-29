<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'ShortMessageService.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['upload_url'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'upload_url.']);
}

$guardianId = $_POST['patient_id'];
$url = $_POST['upload_url'];

$ret = Dbi::getDbi()->addGuardianData($guardianId, $url);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

ShortMessageService::send('13465596133', "数据文件(id：$guardianId)已上传完毕，请确认。");
api_exit_success();
