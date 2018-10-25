<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['examination_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'examination_id.']);
}
if (false === Validate::checkRequired($_POST['doctor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id.']);
}
if (false === Validate::checkRequired($_POST['diagnosis'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'diagnosis.']);
}
if (false === Validate::checkRequired($_POST['value'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'value.']);
}

$info = DbiEcgn::getDbi()->getExaminationInfo($_POST['examination_id']);
if (VALUE_DB_ERROR === $info) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$ret = DbiEcgn::getDbi()->diagnose($_POST['examination_id'], $_POST['doctor_id'], $_POST['diagnosis'], $_POST['value']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

setEcgnCache($info['exam_department_id'], 'diagnosis', $_POST['examination_id']);

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



