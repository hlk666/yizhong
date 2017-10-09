<?php
require_once PATH_LIB . 'DbiAdmin.php';

$agency = isset($_GET['agency']) && !empty($_GET['agency']) ? $_GET['agency'] : null;
$salesman = isset($_GET['salesman']) && !empty($_GET['salesman']) ? $_GET['salesman'] : null;
$level = isset($_GET['level']) && !empty($_GET['level']) ? $_GET['level'] : null;
$hospitalTime = isset($_GET['hospital_time']) && !empty($_GET['hospital_time']) ? $_GET['hospital_time'] : null;
$sTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$eTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] . ' 23:59:59' : null;

$hospitalList = '';
if (!empty($agency)) {
    $ret = DbiAdmin::getDbi()->getHospitalAgency($agency);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    if (empty($ret)) {
        api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
    }
    foreach ($ret as $row) {
        $hospitalList .= $row['hospital_id'] . ',';
    }
    if ($hospitalList != '') {
        $hospitalList = substr($hospitalList, 0, -1);
    }
} elseif (!empty($salesman)) {
    $ret = DbiAdmin::getDbi()->getHospitalSalesman($salesman);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    if (empty($ret)) {
        api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
    }
    foreach ($ret as $row) {
        $hospitalList .= $row['hospital_id'] . ',';
    }
    if ($hospitalList != '') {
        $hospitalList = substr($hospitalList, 0, -1);
    }
} elseif (!empty($level)) {
    $ret = DbiAdmin::getDbi()->getHospitalLevel($level);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    if (empty($ret)) {
        api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
    }
    foreach ($ret as $row) {
        $hospitalList .= $row['hospital_id'] . ',';
    }
    if ($hospitalList != '') {
        $hospitalList = substr($hospitalList, 0, -1);
    }
} elseif (!empty($hospitalTime)) {
    $ret = DbiAdmin::getDbi()->getHospitalTime($hospitalTime);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    if (empty($ret)) {
        api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
    }
    foreach ($ret as $row) {
        $hospitalList .= $row['hospital_id'] . ',';
    }
    if ($hospitalList != '') {
        $hospitalList = substr($hospitalList, 0, -1);
    }
} else {
    //$hospitalList = '1';
}

$ret = DbiAdmin::getDbi()->getGuardiansStatistics($hospitalList, $sTime, $eTime);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    
    foreach ($ret as $key => $row) {
        $ret[$key]['age'] = date('Y') - $row['birth_year'];
        $ret[$key]['sex'] = $row['sex'] == 1 ? '男' : '女';
        unset($ret[$key]['birth_year']);
        
        if ($row['status'] > 3) {
            $ret[$key]['status'] = '已出报告';
        } else {
            $ret[$key]['status'] = '未出报告';
        }
    }
    $result['patients'] = $ret;
    api_exit($result);
}
