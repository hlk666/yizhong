<?php
require_once PATH_LIB . 'db/DbiEcgn.php';

$name = isset($_GET['name']) ? $_GET['name'] : null;
$diagnosisDepartment = isset($_GET['diagnosis_department']) ? $_GET['diagnosis_department'] : null;
$manager = isset($_GET['manager']) ? $_GET['manager'] : null;

$ret = DbiEcgn::getDbi()->getDepartment($name, $diagnosisDepartment, $manager);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['list'] = $ret;
    api_exit($result);
}
