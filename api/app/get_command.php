<?php
require PATH_LIB . 'Invigilator.php';

if (!isset($_GET['patient_id']) || '' == trim($_GET['patient_id'])) {
    echo json_encode(['code' => 1, 'message' => MESSAGE_REQUIRED . 'patient_id']);
    exit;
}
$patientId = $_GET['patient_id'];
if (!file_exists(PATH_CACHE_CMD . $patientId . '.php')) {
    api_exit(['code' => '9', 'message' => 'no command.']);
}

$invigilator = new Invigilator($patientId);
$command = $invigilator->getCommand();

$result = array();
if (empty($command)) {
    api_exit(['code' => '9', 'message' => 'no command.']);
} else {
    $result['code'] = '0';
    $result['command'] = $command;
    $invigilator->clearCommand();
    api_exit($result);
}
