<?php
session_start();
$hosnum = $_SESSION["hospital"];
include ("../libraries/conn.php");
$result=mysql_query("SELECT * FROM friend WHERE ni = '$hosnum' order by f_id  ");
$total=mysql_num_rows($result);
if ($total == 0) {
     mysql_close($conn);
     echo "<script language=javascript>window.location.href='myPatientsList.php?id=$hosnum'</script>";
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
  <?php
if ($_POST['submit1']){
		echo "<script language=javascript>window.location.href='myPatientsList.php?id=$hosnum'</script>"; 
		exit;
}
if ($_POST['submit2']){
		echo "<script language='javascript'> 
			window.location.href='HosList.php?id=$hosnum'
		</script>";
		exit;
}
?>
<form name="" action="" method="post">
<table width="100%"  cellspacing="0" class="Tab"  align="center">
  <tr bgcolor="#4F94CD">
    <td  height="29"><span class="STYLE4">&nbsp;&nbsp;选择查看医院</span></td>
  </tr>
  <tr bgcolor="#B0E2FF">
    <td  align="center" >
	  <input type="submit" name="submit1" value="查看本院注册用户"  style="width:200px"/>&nbsp;&nbsp;
	 </td>
  </tr> 
   <tr bgcolor="#B0E2FF">
    <td  align="center" >
	  <input type="submit" name="submit2" value="查看下级医院注册用户"  style="width:200px"/>&nbsp;&nbsp;
	 </td>
  </tr> 
</table>
</form>
</body>
</html>
