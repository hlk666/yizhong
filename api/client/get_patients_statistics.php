<?php
require_once PATH_LIB . 'DbiAdmin.php';

if (!isset($_GET['hospitals']) || empty($_GET['hospitals'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED .'hospitals']);
}

$hospitalList = $_GET['hospitals'];
$sTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$eTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] . ' 23:59:59' : null;


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
        
        if ($row['status'] == 5) {
            $ret[$key]['status'] = '已出报告';
        } else {
            $ret[$key]['status'] = '未出报告';
        }
    }
    $result['patients'] = $ret;
    api_exit($result);
}
