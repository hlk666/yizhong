<?php
require PATH_LIB . 'DbiAnalytics.php';

if (empty($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$patientId = $_GET['patient_id'];
$diagnosis = DbiAnalytics::getDbi()->getDiagnosisByPatient($patientId);
if (VALUE_DB_ERROR === $diagnosis) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($diagnosis)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

if (file_exists(PATH_CONFIG . 'diagnosis')) {
    $tempArray = array_filter(explode("\r\n", file_get_contents(PATH_CONFIG . 'diagnosis')));
    $mstDiagnosis = array();
    foreach ($tempArray as $temp) {
        $row = explode(',', $temp);
        $mstDiagnosis[$row[0]] = $row[1];
    }
    foreach ($diagnosis as $key => $row) {
        $diagnosis[$key]['diagnosis_name'] = isset($mstDiagnosis[$row['diagnosis_id']]) ? $mstDiagnosis[$row['diagnosis_id']] : '';
    }
}


$ret = array();
$ret['code'] = 0;
$ret['diagnosis'] = $diagnosis;
echo json_encode($ret, JSON_UNESCAPED_UNICODE);
exit;


