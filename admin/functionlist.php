<?php
	session_start();
	if(!$_SESSION["isLogin"] || $_SESSION["loginType"] != 1){
		header("location:../index.php");
		exit;
	}
	$hospital = $_SESSION["hospital"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>功能列表</title>
<style type="text/css">
<!--
.STYLE2 {font-size: 18px}
-->
</style>
</head>

<body bgcolor="#B0E2FF">
<table width="110%" border="0" cellpadding="0" cellspacing="0" style="height:100%;">
<tr>
<td height="60">&nbsp;</td>
</tr>
<tr>
<td height="60" align="center" ><a href="docList.php"  target="reviews" class="STYLE2">医生列表</a></td>
</tr>
<tr>
<td height="60" align="center" ><a href="patientList.php" target="reviews"  class="STYLE2">病人列表</a></td>
</tr>
<tr>
<td height="60" align="center" ><a href="addDoc.php" target="reviews" class="STYLE2">添加医生</a></td>
</tr>
<tr>
<td height="60" align="center" ><a href="setting.php"  target="reviews" class="STYLE2">系统设置</a></td>
</tr>
<tr>
<td height="60" align="center"><a href="logout.php" class="STYLE2" target="_parent">注销登录</a> </td>
</tr>
</table>
</body>
</html>