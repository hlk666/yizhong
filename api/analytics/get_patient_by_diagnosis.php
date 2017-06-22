<?php
require PATH_LIB . 'DbiAnalytics.php';

if (empty($_GET['diagnosis'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'diagnosis.']);
}

$diagnosisList = ' (' . $_GET['diagnosis'] . ') ';
$patients = DbiAnalytics::getDbi()->getPatientByDiagnosis($diagnosisList);
if (VALUE_DB_ERROR === $patients) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($patients)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

foreach ($patients as $key => $row) {
    $patients[$key]['age'] = date('Y') - $row['birth_year'];
    $patients[$key]['sex'] = $row['sex'] == 1 ? '男' : '女';
    unset($patients[$key]['birth_year']);
}

$ret = array();
$ret['code'] = 0;
$ret['patients'] = $patients;
echo json_encode($ret, JSON_UNESCAPED_UNICODE);
exit;


