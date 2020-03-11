<?php
require_once PATH_LIB . 'Validate.php';

$data = array_merge($_GET, $_POST);
if (false === Validate::checkRequired($data['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($data['text'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'text.']);
}

$hospitalId = $data['hospital_id'];
$text = $data['text'];
$file = PATH_DATA . 'hospital_comment' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
file_put_contents($file, $text);

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;

api_exit($result);
