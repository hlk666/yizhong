<?php
require_once PATH_LIB . 'Validate.php';
require PATH_ROOT . 'lib/DbiAnalytics.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['hospital_from'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_from.']);
}
if (false === Validate::checkRequired($_POST['hospital_to'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_to.']);
}
if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}
if (false === Validate::checkRequired($_POST['operator'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'operator.']);
}
if (!is_numeric($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_FORMAT . 'patient_id.']);
}
if (!is_numeric($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_FORMAT . 'type.']);
}

$guardianId = $_POST['patient_id'];
$hospitalFrom = $_POST['hospital_from'];
$hospitalTo = $_POST['hospital_to'];
$type = empty($_POST['type']) ? '0' : $_POST['type'];
$operator = $_POST['operator'];

if (false == DbiAnalytics::getDbi()->existedHospital($hospitalFrom)) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM . 'hospital_from.']);
}
if (false == DbiAnalytics::getDbi()->existedHospital($hospitalTo)) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM . 'hospital_to.']);
}
if ($hospitalFrom == $hospitalTo) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM . '不能从某医院转移到自己。']);
}

$ret = DbiAnalytics::getDbi()->moveData($guardianId, $hospitalFrom, $hospitalTo, $operator, $type);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

clearNotice($hospitalFrom, 'upload_data', $guardianId);

if ($type == '2') {
    $file = PATH_ROOT . 'data' . DIRECTORY_SEPARATOR . 'move_data' . DIRECTORY_SEPARATOR . $hospitalFrom . '.txt';
    
    if (file_exists($file)) {
        $text = file_get_contents($file);
        if (!empty($text)) {
            $text .= ',';
        }
    } else {
        $text = '';
    }
    $text .= $guardianId;
    
    $handle = fopen($file, 'w');
    fwrite($handle, $text);
    fclose($handle);
}
//fix bug happened when moved more than one time.start
if ($type == '2') {
    $file = PATH_ROOT . 'data' . DIRECTORY_SEPARATOR . 'move_data' . DIRECTORY_SEPARATOR . $hospitalTo . '.txt';
    if (file_exists($file)) {
        $text = file_get_contents($file);
        if (strpos($text, $guardianId) === false) {
            //not existed, do nothing.
        } else {
            $text = str_replace(',' . $guardianId, '', $text);
            $text = str_replace($guardianId . ',', '', $text);
            $text = str_replace($guardianId, '', $text);
            file_put_contents($file, $text);
        }
    } else {
        //do nothing.
    }
}
//fix bug happened when moved more than one time.end

setNotice($hospitalTo, 'move_data', $guardianId);

api_exit_success();
