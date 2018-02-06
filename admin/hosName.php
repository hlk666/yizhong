<?php
require '../config/config.php';
require '../lib/DbiAdmin.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$html = '';
if (empty($id)) {
    $html = '';
} else {
    //$ret = DbiAdmin::getDbi()->getHospitalInfo($id);
    $ret = DbiAdmin::getDbi()->getHospitalName($id);
    if (VALUE_DB_ERROR === $ret) {
        $html = '';
    } elseif (empty($ret)) {
        $html = '错误的ID。';
    } else {
        $html = $ret['hospital_name'];
    }
}
echo '
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>羿中医疗科技有限公司管理系统</title>
</head>
<body>' . $html . '</html>';
