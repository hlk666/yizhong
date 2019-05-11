<?php
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';
//require_once PATH_ROOT . 'lib/tool/HpMessage.php';
require_once PATH_LIB . 'ShortMessageService.php';

//2017/04/20
/*
if (isset($_POST['device_type']) && $_POST['device_type'] == '1') {
    if ($_POST['fail_flag'] == '0') {
        setNotice(1, 'phone_data', $_POST['upload_url']);
    }
    api_exit_success();
}
*/
if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

if (false === Validate::checkRequired($_POST['upload_url']) && false === Validate::checkRequired($_POST['fail_flag'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'upload_url.']);
}
/*
if (false === Validate::checkRequired($_POST['device_type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_type.']);
}*/

$guardianId = $_POST['patient_id'];
if (strpos($guardianId, '.') !== false) {
    $guardianId = substr($guardianId, 0, -1);
}

$url = isset($_POST['upload_url']) ? $_POST['upload_url'] : '';
$deviceType = isset($_POST['device_type']) ? $_POST['device_type'] : 0;
$failFlag = isset($_POST['fail_flag']) ? $_POST['fail_flag'] : 0;

$hospitalId = DbiAnalytics::getDbi()->getHospitalByPatient($guardianId);
if (1 == $failFlag) {
    /*
    if (VALUE_DB_ERROR === $hospitalId || empty($hospitalId)) {
        //do nothing.
    } else {
        setNotice($hospitalId, 'upload_data_fail', $guardianId);
    }
    */
    api_exit_success();
} else {
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'upload_data_fail' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
    if (file_exists($file)) {
        clearNotice($hospitalId, 'upload_data_fail', $guardianId);
    }
}
$ret = DbiAnalytics::getDbi()->addGuardianData($guardianId, $url, $deviceType);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$noticeHospital1 = '0';
$noticeHospital2 = '0';
$tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
if (VALUE_DB_ERROR === $tree || array() == $tree) {
    //do nothing.
} else {
    //setNotice($tree['analysis_hospital'], 'upload_data', $guardianId);
    $noticeHospital1 = $tree['analysis_hospital'];
    if ($tree['hospital_id'] != $tree['report_hospital']) {
        //setNotice($tree['report_hospital'], 'upload_data', $guardianId);
        $noticeHospital2 = $tree['report_hospital'];
    }
}
/*
if (1 == $deviceType) {
    $ret = DbiAdmin::getDbi()->appUploadSucceed($guardianId);
    if (VALUE_DB_ERROR === $ret) {
        //api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    if (VALUE_DB_ERROR === $hospitalId || empty($hospitalId)) {
        //do nothing.
    } else {
        setNotice($hospitalId, 'upload_data', $guardianId);
    }
} else {
    $tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
    if (VALUE_DB_ERROR === $tree || array() == $tree) {
        //do nothing.
    } else {
        setNotice($tree['analysis_hospital'], 'upload_data', $guardianId);
        if ($tree['hospital_id'] != $tree['report_hospital']) {
            setNotice($tree['report_hospital'], 'upload_data', $guardianId);
        }
    }
}
*/

$hospitalNotMoveDate = [630, 631, 632];
if (!in_array($hospitalId, $hospitalNotMoveDate)) {
    $isNoticed = moveData($guardianId);
    if (!$isNoticed) {
        if ($noticeHospital1 != '0') {
            setNotice($noticeHospital1, 'upload_data', $guardianId);
        }
        if ($noticeHospital2 != '0') {
            setNotice($noticeHospital2, 'upload_data', $guardianId);
        }
    }
} else {
    if ($noticeHospital1 != '0') {
        setNotice($noticeHospital1, 'upload_data', $guardianId);
    }
    if ($noticeHospital2 != '0') {
        setNotice($noticeHospital2, 'upload_data', $guardianId);
    }
}

api_exit_success();

function moveData($patientId)
{
    $isNoticed = false;
    $hospitalConfig = DbiAnalytics::getDbi()->getReportHospitalByPatient($patientId);
    if ($hospitalConfig === VALUE_DB_ERROR) {
        Logger::write('move_data.log', 'db error when ' . $patientId);
        return false;
    }
    if (empty($hospitalConfig) || $hospitalConfig['report_hospital'] == $hospitalConfig['hospital_id']) {
        return false;
    }
    //滨医心内科
    if ($hospitalConfig['hospital_id'] == '203') {
        return false;
    }
    $hospitalId = $hospitalConfig['report_hospital'];
    //format of file :1,4,10.current index is 1, 4 of 10 need be moved.
    $file = PATH_CONFIG . 'move_data' . DIRECTORY_SEPARATOR . $hospitalId . '.txt';
    if (!file_exists($file)) {
        return false;
    }
    $config = explode(',', file_get_contents($file));
    if (count($config) != 3) {
        Logger::write('move_data.log', 'config error when ' . $patientId);
        return false;
    }
    $index = $config[0];
    $limit = $config[1];
    $total = $config[2];
    
    if ($index <= $limit) {
        $ret = DbiAnalytics::getDbi()->moveData($patientId, $hospitalId, '119', '1', '2');
        if (VALUE_DB_ERROR === $ret) {
            return false;
        }
        
        //clearNotice($hospitalId, 'upload_data', $patientId);
        
        $file1 = PATH_ROOT . 'data' . DIRECTORY_SEPARATOR . 'move_data' . DIRECTORY_SEPARATOR . $hospitalId . '.txt';
        
        if (file_exists($file1)) {
            $text = file_get_contents($file1);
            if (!empty($text)) {
                $text .= ',';
            }
        } else {
            $text = '';
        }
        $text .= $patientId;
        file_put_contents($file1, $text);
        
        setNotice('119', 'move_data', $patientId);
        $isNoticed = true;
    } else {
        //not move data
    }
    //if moved data, add index.
    //if not moved data, also add index because real hospial worked.
    $index++;
    if ($index > $total) {
        $index = 1;
    }
    $newConfig = $index . ',' . $limit . ',' . $total;
    file_put_contents($file, $newConfig);
    Logger::write('move_data.log', 'data moved with ID: ' . $patientId);
    return $isNoticed;
}
