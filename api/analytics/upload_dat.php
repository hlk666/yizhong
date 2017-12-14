<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_GET['size'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'size.']);
}

$data = file_get_contents('php://input');
if (empty($data)) {
    api_exit(['code' => '1', 'message' => '文件流为空。']);
}

$size = strlen($data);
if ($size != $_GET['size']) {
    api_exit(['code' => '1', 'message' => '文件大小不一致。']);
}

$file = PATH_ROOT . 'dat' . DIRECTORY_SEPARATOR . $_GET['patient_id'] . '.dat';
$ret = file_put_contents($file, $data);

if (false === $ret) {
    api_exit(['code' => '5', 'message' => '服务器端写文件失败。']);
}

api_exit_success();
