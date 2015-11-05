<?php
	session_start();
	if($_SESSION["isLogin"]!= true || $_SESSION["loginType"] != 1){
		header("location:../index.php");
		exit;
	}
	$hospital = $_SESSION["hospital"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>管理医生</title>
<script language="javascript">
function CheckPost() {
	if (myform.docNo.value == "") {
		alert("医生工号不能为空！");
		myform.docNo.focus();
		return false;
	}
	if (myform.name.value == "") {
		alert("姓名不能为空！");
		myform.name.focus();
		return false;
	}
	var set =/[^\u4e00-\u9fa5]/;
	if (set.test(myform.name.value)) {
		alert("请输入中文姓名！");
		myform.name.focus();
		return false;
	}
	if (myform.name.value.length > 50) {
		alert("输入的姓名过长");
		myform.name.focus();
		return false;
	}
	if (myform.name.value.length < 2) {
		alert("输入的姓名太短");
		myform.name.focus();
		return false;
	}
	if (myform.psw1.value != null) {
		if (myform.psw1.value.length > 20) {
			alert("输入的密码过长");
			myform.psw1.focus();
			return false;
		}
		if (myform.psw1.value.length < 6) {
			alert("输入的密码太短");
			myform.psw1.focus();
			return false;
		}
		if (myform.psw2.value != form1.psw1.value) {
			alert("两次输入密码不一致");
			myform.psw1.focus();
			return false;
		}
	}
}
</script>
<?php
 if($_POST['Submit1']){
	include ("../libraries/conn.php");
	$originJobNo = $_GET["jobNo"];
	$newJobNo = $_POST["newJobNo"];
	$name = $_POST["name"];	
	$password = $_POST["psw1"];
	if ($originJobNo != $newJobNo) {
		$sql = "SELECT * FROM account WHERE jobNumber = '$newJobNo'";
		$rs = mysql_query($sql) or die ("Query Failed No.1:".mysql_error());
		$num = @mysql_num_rows($rs);
		if ($num > 0) {
  			echo "<script LANGUAGE='javascript'>alert('用户名已存在！');history.go(-1);</script>";
		}
	}
	if ($password == null) {
		$sql = "SELECT * FROM account WHERE jobNumber = '$originJobNo'";
		$rs = mysql_query($sql) or die ("Query Failed No.3:".mysql_error());
		$row = mysql_fetch_array($rs);
		$password = $row[password];
	}
	else
		$password = MD5($password);
	$sql =  "UPDATE account SET name = '$name', jobNumber = '$newJobNo', password = '$password' WHERE jobNumber = '$originJobNo'";
	mysql_query($sql) or die ("Query Failed No.4:".mysql_error());
	mysql_close($conn);
  	echo "<script LANGUAGE='javascript'>alert('修改成功！');history.go(-1);</script>";
 }
?>
<?php
  if($_POST['Submit2']){
	 include ("../libraries/conn.php");
	 $jobNo = $_GET["jobNo"];
	 $sql = "DELETE FROM account WHERE jobNumber = '$jobNo'";
	 mysql_query($sql,$conn) or die ("Query Failed 1:".mysql_error());
	 mysql_close($conn);
	 echo "<script LANGUAGE='javascript'>alert('账户删除成功！');history.go(-2);</script>";
 }
?>
</head>

<body>
<table width="500" height="221" align="center" >
                  <?php
				  	$jobNo = $_GET['jobNo'];
					include ("../libraries/conn.php");
					$sql = "SELECT * FROM account WHERE jobNumber = '$jobNo'";
					$result = mysql_query($sql);
					$row = mysql_fetch_array($result);
				  ?>
                                <form method="post" name="myform" action="" onSubmit="return CheckPost();">
								<tr bgcolor="#4F94CD">
																<td height="36" colspan="3"  >&nbsp;</td>
								  </tr>
												<tr  align="center" bgcolor="#B0E2FF">
												  <td width="134" ><strong>姓名:</strong></td>
																<td width="165" ><?php echo $row[name];?></td>
												  <td width="285" ><input name="name" type="text" style="width: 179px" value="<?php echo $row[name]; ?>"/></td>
								  </tr>
												<tr align="center" bgcolor="#B0E2FF">
												  <td height="32" ><strong>用户名:</strong></td>
																<td ><?php echo $row[jobNumber];?></td>
												  <td ><input name="newJobNo" type="text" style="width: 179px" value="<?php echo $row[jobNumber]; ?>"/></td>
								  </tr>
												<tr align="center" bgcolor="#B0E2FF">
												  <td height="30" ><strong>新密码:</strong></td>
																<td > （不更改请留空）</td>
												  <td ><input name="psw1" type="password" style="width: 179px" value="<?php NULL ?>" /></td>
								  </tr>
												<tr align="center" bgcolor="#B0E2FF">
												  <td height="30" ><strong>确认新密码：</strong></td>
																<td >（不更改请留空）</td>
																<td >
																  <input name="psw2" type="password" style="width: 179px" value="<?php NULL ?>" />
											      </td>
								  </tr>
												
												<tr align="center" bgcolor="#4F94CD">
			
												  <td colspan="3" >
												    <input name="Submit1" type="submit" value="确认修改" style="width: 78px" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											      
												    <input name="Submit2" type="submit" value="删除帐号" style="width: 78px" />
												 </td>
												 
								  </tr>
          </form>
</table>
<?php mysql_close($conn);?>
</body>

</html>
