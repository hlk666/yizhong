<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'function.php';

session_start();
checkHospitalAdminLogin();

$hospitalId = $_SESSION['hospital'];
$creator = $_SESSION['loginId'];

if (isset($_POST['add']) && $_POST['add']){
    $pwd = $_POST['pwd1'];
    $newPwd = $_POST['pwd2'];
    if (empty($pwd) || empty($newPwd) || $pwd != $newPwd) {
        echo "<script language='javascript'>alert('请正确设置密码。');history.back();</script>";
        exit;
    }
    
    $loginName = $_POST['login_name'];
    $isExisted = Dbi::getDbi()->existData('account', ['login_name' => $loginName]);
    if ($isExisted) {
        echo "<script language='javascript'>alert('该登录名已被他人使用。');history.back();</script>";
        exit;
    }
    
    $name = $_POST['name'];
    
    $type = 2;
    $ret = Dbi::getDbi()->addAccount($loginName, $name, $pwd, $type, $hospitalId, $creator);
    if (VALUE_DB_ERROR === $ret) {
        echo "<script language='javascript'>alert('添加医生失败，请重试或联系管理员。');history.back();</script>";
        
    } else {
        echo "<script language='javascript'>alert('添加医生成功。');window.location.href='docList.php'</script>";
    }
    exit;
}
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
  <td width="200" align="center"><input type="text" name="login_name" id="login_name"></td>
</tr>
<tr bgcolor="#B0E2FF">
  <td width="419" height="37" style="width: 200px" align="center">姓名</td>
  <td width="200" align="center"><input type="text" name="name" id="name"></td>
</tr>
<tr bgcolor="#B0E2FF">
  <td width="419" height="42" style="width: 200px" align="center">密码 (6-20位)</td>
  <td width="200" align="center"><input type="password" name="pwd1" id="pwd1" /></td>
</tr>
<tr bgcolor="#B0E2FF">
  <td width="419" height="35" style="width: 200px" align="center">确认密码</td>
  <td width="200" align="center"><input type="password" name="pwd2" id="pwd2" /></td>
</tr>
<tr bgcolor="#4F94CD">
  <td width="419" height="49" align="center" valign="bottom" style="width: 200px; height: 31px">
    <input name="add" type="submit" value="提 交" style="width:150px">
  </td>
  <td width="200" align="center" valign="bottom" style="height: 31px">
    <input name="Button1" type="reset" value="重 置" style="width:150px">
  </td>
</tr>
</form>
<script language="javascript">
function CheckPost() {
    if (form1.login_name.value == "") {
        alert("用户名不能为空！");
        form1.login_name.focus();
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
    if (form1.pwd1.value == "") {
        alert("密码不能为空！");
        form1.pwd1.focus();
        return false;
    }
    if (form1.pwd2.value != form1.pwd1.value) {
        alert("两次输入密码不一致");
        form1.pwd1.focus();
        return false;
    }
}
</script>
</table>
</body>
</html>