<?php
require PATH_LIB . 'DbiAnalytics.php';

if (empty($_GET['hospital_id'])) {
    analytics_exit(['code' => '1', 'message' => MESSAGE_REQUIRED .'hospital_id']);
}

$hospitalId = $_GET['hospital_id'];
if (!is_numeric($hospitalId)) {
    analytics_exit(['code' => '2', 'message' => MESSAGE_FORMAT . 'hospital_id']);
    exit;
}

$ret = DbiAnalytics::getDbi()->getHospitals($hospitalId);
if (VALUE_DB_ERROR === $ret) {
    analytics_exit(['code' => '3', 'message' => 'error']);
}

$hospitalIdList = '';
foreach ($ret as $row) {
    $hospitalIdList .= $row['hospital_id'] . ',';
}
$hospitalIdList .= $hospitalId;

$patientName = isset($_GET['patient_name']) ? $_GET['patient_name'] : null;
$startTime = isset($_GET['start_time']) ? $_GET['start_time'] : null;
$endTime = isset($_GET['end_time']) ? $_GET['end_time'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;
$hbiDoctor = isset($_GET['hbi_doctor']) ? $_GET['hbi_doctor'] : null;
$reportDoctor = isset($_GET['report_doctor']) ? $_GET['report_doctor'] : null; 
$patients = DbiAnalytics::getDbi()->getPatients($hospitalIdList, $patientName, $startTime, $endTime, $status, $hbiDoctor, $reportDoctor);
if (VALUE_DB_ERROR === $patients) {
    analytics_exit(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
}
foreach ($patients as $key => $row) {
    $patients[$key]['age'] = date('Y') - $row['birth_year'];
    $patients[$key]['sex'] = $row['sex'] == 1 ? '男' : '女';
    unset($patients[$key]['birth_year']);
}

foreach ($patients as $key => $value) {
    if ($value['status'] == 3) {
        $patients[$key]['status'] = '已上传';
    } elseif ($value['status'] == 4) {
        $patients[$key]['status'] = '已分析';
    } elseif ($value['status'] == 5) {
        $patients[$key]['status'] = '已出报告';
    } else {
        $patients[$key]['status'] = '未上传';
    }
    
    if (file_exists(PATH_HBI . $value['patient_id'] . '.hbi')) {
        $patients[$key]['hbi'] = '是';
    } else {
        $patients[$key]['hbi'] = '否';
    }
}

$ret = array();
$ret['code'] = 0;
$ret['patients'] = $patients;
analytics_exit($ret);

function analytics_exit(array $ret)
{
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}
