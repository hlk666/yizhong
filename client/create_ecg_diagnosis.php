<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">   
<html xmlns="http://www.w3.org/1999/xhtml">   
<head>   
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta http-equiv="refresh" content="60">   
<title>插入诊断</title>   
</head>
<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';

$guardianId = $_GET["id"];
$ecgId = $_GET["eid"];
$content = $_GET["rx"];
$doctorName = $_GET["docNo"];
$doctorId = Dbi::getDbi()->getAllData('select account_id from account where real_name = "' . $doctorName . '"');
var_dump($doctorId);exit;
if (VALUE_DB_ERROR === $doctorId) {
    echo '访问数据库失败，请重试或联系管理员。';
    exit;
}
if (empty($doctorId)) {
    echo '该医生尚未在系统中注册，请通过医院管理注册该医生信息。';
    exit;
}
$ret = Dbi::getDbi()->createDiagnosis($ecgId, $doctorId[0]['account_id'], $content);
if (VALUE_DB_ERROR === $ret) {
    echo '访问数据库失败，请重试或联系管理员。';
    exit;
}
header("location:get_guardian_diagnosis.php?id=".$guardianId);
exit;
?>
</html>