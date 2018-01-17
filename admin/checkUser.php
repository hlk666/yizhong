<?php
require '../config/config.php';
require '../lib/DbiAdmin.php';

$user = isset($_GET['user']) ? $_GET['user'] : '';
$html = '';
if (empty($user)) {
    $html = '';
} elseif (is_numeric($user)) {
    $html = '登录名不能全是数字';
} else {
    $isExisted = DbiAdmin::getDbi()->existedLoginName($user, 0);
    if (VALUE_DB_ERROR === $isExisted) {
        $html = '';
    } elseif (true === $isExisted) {
        $html = '该登录名已被使用。';
    }  else {
        $html = '';
    }
}
echo '
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>羿中医疗科技有限公司管理系统</title>
</head>
<body>' . $html . '</html';
