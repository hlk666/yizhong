<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['room_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'room_id.']);
}

$id = $_POST['room_id'];
$ret = DbiEcgn::getDbi()->existedRoom($id);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (false === $ret) {
    api_exit(['code' => '18', 'message' => '对象不存在。']);
}

$name = isset($_POST['name']) ? $_POST['name'] : null;

$data = array();
if (null !== $name) {
    $data['room_name'] = $name;
}

if (empty($data)) {
    api_exit(['code' => '1', 'message' => '没有修改任何信息。']);
}

$ret = DbiEcgn::getDbi()->editRoom($id, $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
