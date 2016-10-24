<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (!isset($_GET['name']) && !isset($_GET['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name or tel.']);
}
if (isset($_GET['name']) && false === Validate::checkRequired($_GET['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}
if (isset($_GET['tel']) && false === Validate::checkRequired($_GET['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}

$name = isset($_GET['name']) ? $_GET['name'] : null;
$tel = isset($_GET['tel']) ? $_GET['tel'] : null;

$reports = Dbi::getDbi()->getPatientByNameAndTel($name, $tel);
if (VALUE_DB_ERROR === $reports) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($reports)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['reports'] = $reports;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
