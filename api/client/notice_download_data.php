<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}

$guardianId = $_POST['patient_id'];
$ret = Dbi::getDbi()->getDownloadData($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
/*
if (empty($ret) || empty($ret['download_start_time'])) {
    api_exit(['code' => '20', 'message' => '该用户文件尚未开始下载。']);
}*/

$type = $_POST['type'];
$data = array();

if ($type == '1') {
    $data['download_end_time'] =  date('Y-m-d H:i:s');
} elseif ($type == '2'){
    $status = Dbi::getDbi()->getDataStatus($guardianId);
    if (VALUE_DB_ERROR === $status) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    if ($status == 3) {
        $data['download_start_time'] = 'null';
        $data['download_end_time'] = 'null';
        $data['status'] = 2;
        $data['download_doctor'] = 0;
        
        $tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
        if (VALUE_DB_ERROR === $tree || array() == $tree) {
            //do nothing.
        } else {
            setNotice($tree['analysis_hospital'], 'upload_data', $guardianId);
            if ($tree['hospital_id'] != $tree['report_hospital']) {
                setNotice($tree['report_hospital'], 'upload_data', $guardianId);
            }
        }
    } else {
        //do nothing.
    }
} else {
    api_exit(['code' => '2', 'message' => MESSAGE_PARAM]);
}

$ret = Dbi::getDbi()->noticeDownloadData($guardianId, $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
