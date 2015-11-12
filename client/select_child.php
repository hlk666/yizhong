<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';

session_start();
$hospitalId = $_GET['id'];
$child = Dbi::getDbi()->getChildHospitals($hospitalId);
if (empty($child)) {
    echo "<script language=javascript>alert(\"没有下级医院\");history.back();</script>";
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>监护状态</title>
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
<form name="form_select_hospital" method="post">
<table width="100%"  cellspacing="0" class="Tab"  align="center">
  <tr bgcolor="#4F94CD">
    <td  height="29"><span class="STYLE4">&nbsp;&nbsp;选择查看医院</span></td>
  </tr>
  <tr>
    <td colspan="3"><select name="custody_hos" style="width: 100%" id="custody_hos">
    <?php
    echo '<option value="0" selected="selected"></option>';
    foreach ($child as $value) {
        echo '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
    }
    ?>
    </select></td>
  </tr>
  <tr bgcolor="#B0E2FF">
    <td align="center" >
      <input type="button" name="select" value="确定"  style="width:100px" onclick="Select()" />&nbsp;&nbsp;
      <input type="button" name="return" value="返回"  style="width:100px" onclick="javascript:history.back();" />
    </td>
  </tr> 
</table>
</form>
<script language="javascript">
function Select() {
    if (form_select_hospital.custody_hos.value == "0") {
        alert("请选择下级医院。");
    } else {
        window.location.href='patient_list.php?current_flag=0&id=' + form_select_hospital.custody_hos.value;
    }
}
</script>
</body>
</html>