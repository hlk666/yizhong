<?php
require_once PATH_LIB . 'DbiAdmin.php';

$hospitals = isset($_GET['hospitals']) ? $_GET['hospitals'] : null;


$ret = DbiAdmin::getDbi()->getNoticeRule($hospitals);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'list' => $ret]);
