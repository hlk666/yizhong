<?php
require_once PATH_LIB . 'DbiAdmin.php';

$count = isset($_GET['count']) && !empty($_GET['count']) ? $_GET['count'] : null;
$type = isset($_GET['type']) && !empty($_GET['type']) ? $_GET['type'] : null;
$level = isset($_GET['level']) && !empty($_GET['level']) ? $_GET['level'] : null;

$ret = DbiAdmin::getDbi()->getExamQuestion($count, $type, $level);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'list' => $ret]);
