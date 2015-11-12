<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'function.php';

session_start();
checkDoctorLogin();

$hospitalId = $_SESSION["hospital"];
$ret = Dbi::getDbi()->getChildHospitals($hospitalId);
if (empty($ret)) {
    echo "<script language=javascript>window.location.href='patient_list.php?current_flag=1&id=$hospitalId'</script>";
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>用户管理</title>
<style type="text/css">
<!--
.STYLE4 {
    font-family: "宋体";
    font-weight: bold;
}
.STYLE5 {font-size: 14px}
.Tab{ border-collapse:collapse; height:100%;}
.Tab td{ border:solid 1px #0000EE}
-->
</style>
</head>
<body>
<?php
if (isset($_POST['current_hospital']) && $_POST['current_hospital']){
    echo "<script language=javascript>window.location.href='patient_list.php?current_flag=1&id=$hospitalId'</script>"; 
    exit;
}
if (isset($_POST['child_hospital']) && $_POST['child_hospital']){
    echo "<script language='javascript'>window.location.href='select_child.php?id=$hospitalId'</script>";
    exit;
}
?>
<form name="menu" action="" method="post">
<table width="100%"  cellspacing="0" class="Tab"  align="center">
  <tr bgcolor="#4F94CD">
    <td  height="29"><span class="STYLE4">&nbsp;&nbsp;选择查看医院</span></td>
  </tr>
  <tr bgcolor="#B0E2FF">
    <td align="center" >
      <input type="submit" name="current_hospital" value="查看本院注册用户"  style="width:200px"/>&nbsp;&nbsp;
     </td>
  </tr> 
   <tr bgcolor="#B0E2FF">
    <td  align="center" >
      <input type="submit" name="child_hospital" value="查看下级医院注册用户"  style="width:200px"/>&nbsp;&nbsp;
     </td>
  </tr> 
</table>
</form>
</body>
</html>