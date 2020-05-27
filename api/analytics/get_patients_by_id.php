<?php
require PATH_LIB . 'DbiAnalytics.php';

if (empty($_GET['patient_list'])) {
    analytics_exit(['code' => '1', 'message' => MESSAGE_REQUIRED .'patient_list']);
}

$patientIdList = $_GET['patient_list'];
$arrayId = explode(',', $patientIdList);
$newPatientList = array();
foreach ($arrayId as $id) {
    if (!is_numeric($id)) {
        analytics_exit(['code' => '1', 'message' => 'ID列表必须为数字。']);
    }
    if ($id < 30000) {
        $newPatientList[] = $id + 65536;
    } else {
        $newPatientList[] = $id;
    }
}
$newPatientString = implode(',', $newPatientList);

$patients = DbiAnalytics::getDbi()->getPatientsByIdForAnalytics($newPatientString);
if (VALUE_DB_ERROR === $patients) {
    analytics_exit(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
}
foreach ($patients as $key => $row) {
    $patients[$key]['age'] = date('Y') - $row['birth_year'];
    $patients[$key]['sex'] = $row['sex'] == 1 ? '男' : '女';
    unset($patients[$key]['birth_year']);
}

//$sortHbi = array();
//$sortId = array();
foreach ($patients as $key => $value) {
    if (file_exists(PATH_HBI . $value['patient_id'] . '.hbi')) {
        $patients[$key]['hbi'] = '是';
        //$sortHbi = 1;
    } else {
        $patients[$key]['hbi'] = '否';
        //$sortHbi = 0;
    }
    //$sortId = $value['patient_id'];
}

//array_multisort($sortHbi, SORT_NUMERIC, SORT_DESC, $sortId, SORT_NUMERIC, SORT_DESC, $patients);

$ret = array();
$ret['code'] = 0;
$ret['patients'] = $patients;
analytics_exit($ret);

function analytics_exit(array $ret)
{
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}