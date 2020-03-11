<?php
require_once PATH_LIB . 'Validate.php';

$data = array_merge($_GET, $_POST);
if (false === Validate::checkRequired($data['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}

$hospitalId = $data['hospital_id'];
$file = PATH_DATA . 'hospital_comment' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$text = '';
if (file_exists($file)) {
    $text = file_get_contents($file);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['text'] = $text

api_exit($result);
