<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">   
<html xmlns="http://www.w3.org/1999/xhtml">   
<head>   
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta http-equiv="refresh" content="60">   
<title>插入诊断</title>   
</head>
<?php
$id = $_GET["id"];
$eid = $_GET["eid"];
$rx = $_GET["rx"];
$Docname = $_GET["docNo"];
$hospital = 1;
    include ("../libraries/conn.php");
    $rs = mysql_query("SELECT * FROM `account` WHERE `name` = '$Docname'",$conn) or die(mysql_error());
    if (mysql_num_rows($rs) == 0)
    {
        $message = '该医生尚未在系统中注册，请通过医院管理注册医生!';
        mysql_close($conn);
        echo $message;
        exit;
    }
    else
    {
        $row = mysql_fetch_array($rs);
        $jobNo = $row[id];
        $sql = "INSERT INTO `remote_ecg`.`diagnosis` (`d_id`, `p_id`, `e_id`, `docID`,`h_id`,`content`, `diaTime`) VALUES ('', '$id','$eid', '$jobNo', '$hospital','$rx',  CURRENT_TIMESTAMP);";
         mysql_query($sql,$conn) or die (mysql_error());
         mysql_close($conn);
    }
    header("location:./hdiagnose.php?id=".$id);
    exit;
?>
</html>  

