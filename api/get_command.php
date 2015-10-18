<?php
require '../config/path.php';
require PATH_LIB . 'Invigilator.php';

if (!isset($_GET['patient_id'])) {
    echo 'param error.';
    exit;
}
$patientId = $_GET['patient_id'];

$file = PATH_CACHE_CMD . $patientId . '.php';
$invigilator = new Invigilator($patientId);
$command = $invigilator->getCommand();

$result = array();
if (empty($command)) {
    $result['code'] = 9;
} else {
    $result['code'] = 0;
    $result['command'] = $command;
    $invigilator->clearCommand();
}

echo json_encode($result);
