<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';

$guardianId = $_GET["id"];
include ("../libraries/conn.php");
    $sql = "SELECT * FROM patient_health_info WHERE p_id = '$guardianId'";
    $result=mysql_query($sql,$conn);
    $row = mysql_fetch_array($result);
    $allergyHistory = $row[allergyHistory];
    mysql_close($conn);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>修改报告</title>
<style type="text/css">
<!--
.STYLE3 {
       border: 1px solid #808080;
       background-color: #B0E2FF;
}
.STYLE4 {
    font-family: "宋体";
    font-weight: bold;
}
.STYLE5 {font-size: 14px}
.Tab{ border-collapse:collapse; }
.Tab td{ border:solid 1px #0000EE}
-->
</style>
</head>
<body>
  <?php
if ($_POST['submit1']){
    if ($_POST['rx']=="") {
        echo "<script LANGUAGE='javascript'>alert('诊断内容不能为空！');</script>";
    }
    else {
        $rx = $_POST['rx'];
        include ("../libraries/connUsersData.php");
        $sql = "UPDATE `remote_ecg`.`patient_health_info` SET `allergyHistory` = '$rx' WHERE `p_id` ='$guardianId'";
        mysql_query($sql, $connU) or die ("Query Failed No.4:".mysql_error());
        mysql_close($connU);
        header("location:./illsum.php?id=$guardianId");
        exit;
    }    
}
?>
<form name="" action="" method="post">
<table width="100%" height="100%" cellspacing="0" class="Tab"  align="center">
  <tr bgcolor="#4F94CD" height="10%" >
    <td colspan="2"><span class="STYLE4">&nbsp;&nbsp;修改病情总结</span></td>
  </tr>
  <tr height="80%" class="STYLE3">
    <td colspan="2" align="center"><textarea name="rx" id="rx" cols="40" rows="5" ><?php echo $allergyHistory;?></textarea></td>
  </tr>
  <tr class="STYLE3" height="10%">
    <td  colspan="2"  align="center" >
      <input type="submit" name="submit1" value="提  交"  style="width:100px"/> 
      &nbsp;&nbsp; <input type="reset" name="submit2" value="重  置"  style="width:100px"/> </td>
  </tr>
</table>
 </form>
</body>
</html>
