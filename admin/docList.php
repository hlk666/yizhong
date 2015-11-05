<?php
	session_start();
	if(!$_SESSION["isLogin"] || $_SESSION["loginType"] != 1){
		header("location:../index.php");
		exit;
	}
	$hospital = $_SESSION["hospital"];
?>
<span class="style7">﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"></span>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>医生列表</title>
<style type="text/css">
BODY {margin: 1px}
#scroll_table{ height:100%; overflow:auto;}
table{border-collapse:collapse; }
table thead{background-color:#FFFFFF}
th,td{border:1px solid #CCC}
#thead{ position:fixed; z-index:100;background-color:#FFF}
</style>
<body>
<div style="width: 100%"  align="center">
<?php
        include ("../libraries/page.php");
		include ("../libraries/conn.php");
		include ("../libraries/htmtocode.php");
								
		$result=mysql_query("SELECT * FROM `account` WHERE userType = 2 AND hospitalNumber = '$hospital'");
		$total=mysql_num_rows($result);
								//调用pageft()，每页显示10条信息（使用默认的20时，可以省略此参数），使用本页URL（默认，所以省略掉）。
		_PAGEFT($total,20);
		echo $pagenav;
								
		$result=mysql_query("SELECT * FROM `account` WHERE userType = 2 AND hospitalNumber = '$hospital' limit $firstcount,$displaypg ");
								
		echo "<table style='width:400px;'  id='data_table' >";
		echo "<tr bgcolor=#555555><td align='center'>用户名</td><td align='center'>姓名</td></tr>";
		$i = 1;
	     while($row=mysql_fetch_array($result)){
		 if ($i % 2 == 0){
         $color='#E5E5E5';
		 }else{
		 $color='';
		 } $i += 1;
      echo "<tr bgcolor=$color>
	   <td><div align='center' style='width:200px; height:20px'><a href = './editDoc.php?jobNo=$row[jobNumber]'>".$row[jobNumber]."</div>
	   </td>
	   <td><div align='center' style='width:200px; height:20px'>".$row[name]."</div>
	   </td>
	   </tr>";
         }
      echo "</table>";
?>
</div>
</body>
</html>
