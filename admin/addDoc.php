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
<title>添加医生</title>
</head>

<body>
<table width="427" height="261" align="center" class="style18" >
	<tr bgcolor="#4F94CD">
         <td width="419" height="38" colspan="2" class="style7"> &nbsp;&nbsp;<strong>您的位置</strong>：添加医生</td>
    </tr>
         <form name="form1" method="post" action="" onSubmit="return CheckPost()">
    <tr bgcolor="#B0E2FF" >
        <td width="419" height="34" style="width: 200px" align="center">用户帐号</td>
        <td width="200" align="center"><input type="text" name="docNo" id="docNo"></td>
   </tr>
   <tr bgcolor="#B0E2FF">
        <td width="419" height="37" style="width: 200px" align="center">姓名</td>
        <td width="200" align="center"><input type="text" name="name" id="name"></td>
   </tr>
   <tr bgcolor="#B0E2FF">
    <td width="419" height="42" style="width: 200px" align="center">密码 (6-20位)</td>
    <td width="200" align="center"><input type="password" name="psw1" id="psw1" /></td>
  </tr>
  <tr bgcolor="#B0E2FF">
    <td width="419" height="35" style="width: 200px" align="center">确认密码</td>
    <td width="200" align="center"><input type="password" name="psw2" id="psw2" /></td>
  </tr>
  <tr bgcolor="#4F94CD">
     <td width="419" height="49" align="center" valign="bottom" style="width: 200px; height: 31px">
      <input name="Submit1" type="submit" value="提 交" style="width:150px"></td>
    <td width="200" align="center" valign="bottom" style="height: 31px">
       <input name="Button1" type="reset" value="重 置" style="width:150px"></td>
  </tr>
</form>

<script language="javascript">

function CheckPost() {
	if (form1.docNo.value == "") {
		alert("用户名不能为空！");
		form1.docNo.focus();
		return false;
	}
	if (form1.name.value == "") {
		alert("姓名不能为空！");
		form1.name.focus();
		return false;
	}
	var set =/[^\u4e00-\u9fa5]/;
	if (set.test(form1.name.value)) {
		alert("请输入中文姓名！");
		form1.name.focus();
		return false;
	}
	if (form1.name.value.length > 50) {
		alert("输入的姓名过长");
		form1.name.focus();
		return false;
	}
	if (form1.name.value.length < 2) {
		alert("输入的姓名太短");
		form1.name.focus();
		return false;
	}
	if (form1.psw1.value.length > 20) {
		alert("输入的密码过长");
		form1.psw1.focus();
		return false;
	}
	if (form1.psw1.value.length < 6) {
		alert("输入的密码太短");
		form1.psw1.focus();
		return false;
	}
	if (form1.psw2.value != form1.psw1.value) {
		alert("两次输入密码不一致");
		form1.psw1.focus();
		return false;
	}
}
</script>
                <?php
					if ($_POST['Submit1']){
						$docNo = $_POST['docNo'];
						$name = $_POST['name'];
						$psw = MD5($_POST['psw1']);
						$type = 2;
						
						include ("../libraries/conn.php");
						
						$sql = "SELECT * FROM account WHERE jobNumber = '$docNo'";
						$rs = mysql_query($sql) or die ("Query failed: " .mysql_error());
						$num = mysql_num_rows($rs);
						if ($num > 0){
							echo "<script language='javascript'>alert('医生用户名已存在！');history.back();</script>";
							mysql_close($conn);
							exit;
						}
						
						$sql = "INSERT INTO `remote_ecg`.`account` (jobNumber, password, name, userType, hospitalNumber) VALUES ('$docNo', '$psw', '$name', '$type', '$hospital');";
						mysql_query($sql,$conn) or die ("Query Failed 1:".mysql_error());
						mysql_close($conn);
						echo "<script language='javascript'> 
							alert('医生添加成功！');
							this.location.href='./addDoc.php';
						</script>";
					}
				?>
</table>
</body>
</html>
