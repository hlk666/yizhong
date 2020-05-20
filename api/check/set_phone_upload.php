<?php
require_once PATH_LIB . 'Validate.php';

$data = array_merge($_GET, $_POST);
if (false === Validate::checkRequired($data['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($data['add'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'add.']);
}

$hospitalId = $data['hospital_id'];
$add = $data['add'];
$file = PATH_DATA . 'phone_upload.txt';
$list = explode(',', file_get_contents($file));
foreach ($list as $item) {
    if (!empty($item) && $item != $hospitalId) {
        $data[] = $item;
    }
}
if ($add = '1') {
    $data[] = $hospitalId;
}
file_put_contents($file, implode(',', $data));

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;

api_exit($result);
