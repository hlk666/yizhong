<?php
$deviceId = isset($_GET['device_id']) ? $_GET['device_id'] : '';

$result = array();
if ($deviceId == '1') {
    $result['code'] = 0;
    $result['patient_id'] = 123;
    $result['name'] = '张三';
    $result['mode'] = 1;
} elseif ($deviceId == '2') {
    $result['code'] = 0;
    $result['patient_id'] = 234;
    $result['name'] = '李四';
    $result['mode'] = 2;
} elseif ($deviceId == '3') {
    $result['code'] = 0;
    $result['patient_id'] = 345;
    $result['name'] = '王五';
    $result['mode'] = 3;
} else {
    $result['code'] = 1;
    $result['message'] = 'some error message';
}
echo json_encode($result);