<?php
require '../config/path.php';
require PATH_LIB . 'Invigilator.php';

if (!isset($_GET['patient_id']) && !isset($_POST['patient_id'])) {
    echo 'param error.';
    exit;
}

$data = array_merge($_GET, $_POST);

$patientId = $data['patient_id'];
$mode = isset($data['mode']) ? $data['mode'] : '0';

$invigilator = new Invigilator($patientId, $mode);
$invigilator->create($data);

echo json_encode(array('code' => '0'));
exit;
