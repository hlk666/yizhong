<?php
require '../config/config.php';
require '../lib/DbiAdmin.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$html = $id;

$html = '';
if (empty($id)) {
    $html = '';
} else {
    $invoiceEndDate = DbiAdmin::getDbi()->getHospitalInvoice($id);
    if (VALUE_DB_ERROR === $invoiceEndDate) {
        $html = '';
    } elseif (empty($invoiceEndDate)) {
        $html = '无';
    } else {
        $html = $invoiceEndDate;
    }
}

echo '
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>羿中医疗科技有限公司管理系统</title>
</head>
<body>' . $html . '</html>';
