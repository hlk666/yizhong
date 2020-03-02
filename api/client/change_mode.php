<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Invigilator.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['old_mode'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'old_mode.']);
}
if (false === Validate::checkRequired($_POST['new_mode'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'new_mode.']);
}
$guardianId = $_POST['patient_id'];
$oldMode = $_POST['old_mode'];
$newMode = $_POST['new_mode'];

$ret = Dbi::getDbi()->existedOldMode($guardianId, $oldMode);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (false === $ret) {
    api_exit(['code' => '18', 'message' => '该操作对象不存在。']);
}

$ret = Dbi::getDbi()->changeMode($guardianId, $oldMode, $newMode);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$invigilator = new Invigilator($guardianId);
$ret = $invigilator->create(['new_mode' => $newMode]);

if (VALUE_PARAM_ERROR === $ret) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}

updateMode($guardianId, $newMode);

if (VALUE_GT_ERROR === $ret) {
    api_exit(['code' => '3', 'message' => MESSAGE_GT_ERROR]);
}

api_exit_success();

function updateMode($id, $mode)
{
    $file = PATH_DATA . 'mode.txt';
    $list = explode(';', file_get_contents($file));
    $data = array();
    foreach ($list as $item) {
        if (empty($item)) {
            continue;
        }
        $tmp = explode(',', $item);
        if (isset($tmp[0]) && $tmp[0] != $id) {
            $data[] = $item;
        }
    }
    $data[] = $id . ',' . $mode;
    file_put_contents($file, implode(';', $data));
}
