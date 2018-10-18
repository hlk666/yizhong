<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

$data = file_get_contents('php://input');

if (false === Validate::checkRequired($_GET['examination_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'examination_id.']);
}
if (false === Validate::checkRequired($_GET['size'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'size.']);
}
if (false === Validate::checkRequired($_GET['doctor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id.']);
}
if (!isset($data) || trim($data) == '') {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'data.']);
}
if (strlen($data) != $_GET['size']) {
    api_exit(['code' => '1', 'message' => '数据传输中出现丢包，请重新上传。']);
}

$id = $_GET['examination_id'];
$dir = PATH_ROOT . 'ecgn_data' . DIRECTORY_SEPARATOR;
if (!is_dir($dir)) {
    mkdir($dir);
}
$file = $id . '_' . date('YmdHis') . '.bin';
$urlFile = 'ecgn_data/' . $file;
$retIO = file_put_contents($dir . $file, $data);
if ($retIO === false) {
    Logger::write($this->logFile, 'failed to save file on ID of :' . $id);
    api_exit(['code' => '1', 'message' => '服务器IO错误。']);
}

$info = DbiEcgn::getDbi()->getExaminationInfo($_GET['examination_id']);
if (VALUE_DB_ERROR === $info) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$ret = DbiEcgn::getDbi()->examine($_GET['examination_id'], $_GET['doctor_id'], $urlFile);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
$text = $_GET['examination_id'] . ',' . $info['patient_name'] . ',' . $info['exam_name'];
setEcgnCache($info['diagnosis_department_id'], 'diagnosis', $text);
setEcgnCache($info['exam_department_id'], 'call_after_examin', $info['patient_name']);

api_exit_success();

function setEcgnCache($departmentId, $type, $text, $separator = ';')
{
    $dir = PATH_ROOT . 'ecgn_cache' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
    if (!is_dir($dir)) {
        mkdir($dir);
    }
    $file = $dir . $departmentId . '.txt';
    if (!file_exists($file)) {
        file_put_contents($file, $text);
    } else {
        $oldArray = explode($separator, file_get_contents($file));
        $oldArray[] = $text;
        $newArray = array_unique($oldArray);
        file_put_contents($file, implode($separator, $newArray));
    }
}



