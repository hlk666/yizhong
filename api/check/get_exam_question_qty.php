<?php
require_once PATH_LIB . 'DbiAdmin.php';

$type = DbiAdmin::getDbi()->getExamQuestionQty('type');
if (VALUE_DB_ERROR === $type) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($type)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

$miniType = DbiAdmin::getDbi()->getExamQuestionQty('mini_type');
if (VALUE_DB_ERROR === $miniType) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($miniType)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['type'] = $type;
$result['mini_type'] = $miniType;
api_exit($result);

