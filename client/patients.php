<?php
require '../common.php';
include_head('用户管理');
session_start();
checkDoctorLogin();

$hospitalId = $_SESSION["hospital"];
if (isset($_POST['current_hospital']) && $_POST['current_hospital']){
    user_goto(null, GOTO_FLAG_URL, 'patient_list.php?current_flag=1&id=' . $hospitalId);
}
if (isset($_POST['child_hospital']) && $_POST['child_hospital']){
    user_goto(null, GOTO_FLAG_URL, 'select_child.php?id=' . $hospitalId);
}
$ret = Dbi::getDbi()->getHospitalChild($hospitalId);
if (empty($ret)) {
    user_goto(null, GOTO_FLAG_URL, 'patient_list.php?current_flag=1&id=' . $hospitalId);
}

?>
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
<body>
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