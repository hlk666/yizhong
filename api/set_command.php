<?php
require PATH_LIB . 'Invigilator.php';

$data = array_merge($_GET, $_POST);
if (!isset($data['patient_id']) || '' == trim($data['patient_id'])) {
    echo json_encode(['code' => 1, 'message' => MESSAGE_REQUIRED . 'patient_id']);
    exit;
}

$guardianId = $data['patient_id'];

$invigilator = new Invigilator($guardianId);
$ret = $invigilator->create($data);
if (VALUE_PARAM_ERROR === $ret) {
    echo json_encode(['code' => 1, 'message' => MESSAGE_PARAM]);
} elseif (VALUE_DB_ERROR === $ret) {
    echo json_encode(['code' => 2, 'message' => MESSAGE_DB_ERROR]);
} elseif (VALUE_GT_ERROR === $ret) {
    echo json_encode(['code' => 3, 'message' => MESSAGE_GT_ERROR]);
} else {
    echo json_encode(array('code' => '0', 'message' => ''));
}
