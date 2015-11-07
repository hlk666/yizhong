<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'function.php';

session_start();
checkHospitalAdminLogin();
$hospitalId = $_SESSION['hospital'];

if (isset($_POST['edit'])){
    $oldName = $_POST['oldHospitalName'];
    $newName = $_POST['hospitalName'];
    $oldLoginName = $_POST['oldLoginName'];
    $newLoginName = $_POST['loginName'];
    $oldAddress = $_POST['oldAddress'];
    $newAddress = $_POST['address'];
    $oldTel = $_POST['oldTel'];
    $newTel = $_POST['tel'];
    $password = $_POST['pwd1'];
    
    $dataAccount = array();
    $dataHospital = array();
    if ($oldLoginName != $newLoginName) {
        $isExisted = Dbi::getDbi()->existData('account', ['login_name' => $newLoginName]);
        if (VALUE_DB_ERROR === $isExisted) {
            echo "<script language='javascript'>alert('数据库访问失败，请重试。');history.back();</script>";
            exit;
        }
        if ($isExisted) {
            echo "<script language='javascript'>alert('该账号已被他人使用。');history.back();</script>";
            exit;
        }
        $dataAccount['login_name'] = $newLoginName;
    }
    if (!empty($password)) {
        $dataAccount['password'] = md5($password);
    }
    if ($oldName != $newName) {
        $dataHospital['hospital_name'] = $newName;
    }
    if ($oldAddress != $newAddress) {
        $dataHospital['address'] = $newAddress;
    }
    if ($oldTel != $newTel) {
        $dataHospital['tel'] = $newTel;
    }
    if (empty($dataAccount) && empty($dataHospital)) {
        echo "<script language='javascript'>alert('没有修改任何信息，请不要提交。');history.back();</script>";
        exit;
    }
    //@todo add transcation.
    if (!empty($dataAccount)) {
        $ret = Dbi::getDbi()->editAccount($doctorId, $dataAccount);
        if (VALUE_DB_ERROR === $ret) {
            echo "<script language='javascript'>alert('操作失败，请重试。');history.back();</script>";
            exit;
        }
    }
    if (!empty($dataHospital)) {
        $ret = Dbi::getDbi()->editHospital($hospitalId, $dataHospital);
        if (VALUE_DB_ERROR === $ret) {
            echo "<script language='javascript'>alert('操作失败，请重试。');history.back();</script>";
            exit;
        }
    }
    
    echo "<script language='javascript'>alert('修改成功。');window.location.href='setting.php'</script>";
    exit;
}

$info = Dbi::getDbi()->getHospitlAdminInfo($hospitalId);
$ret = checkDataFromDB($info);
if ($ret !== true) {
    echo $ret;
    exit;
}
$loginName = $info['login_name'];
$hospitalName = $info['hospital_name'];
$address = $info['address'];
$tel = $info['tel'];
?>
<span class="style7">﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"></span>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
<title>系统设置</title>
</head>
<body>
<form name="form1" method="post" action="" onSubmit="return CheckPost()">
<input type="hidden" name="oldLoginName" value="<?php echo $loginName; ?>" />
<input type="hidden" name="oldHospitalName" value="<?php echo $hospitalName; ?>" />
<input type="hidden" name="oldAddress" value="<?php echo $address; ?>" />
<input type="hidden" name="oldTel" value="<?php echo $tel; ?>" />
<table width="620" align="center" >
  <tr bgcolor="#4F94CD">
    <td height="36" colspan="3"><strong>&nbsp;&nbsp;医院信息管理</strong></td>
  </tr>
  <tr align="center" bgcolor="#B0E2FF">
    <td width="200" height="30" >管理员账号 </td>
    <td width="200">
      <input name="loginName" type="text" id="loginName" value="<?php echo $loginName; ?>" /></td>
    <td width="200">(字母,数字组合)</td>
  </tr>
  <tr align="center" bgcolor="#B0E2FF">
    <td width="200" height="30" >医院名称</td>
    <td colspan="2"><input name="hospitalName" type="text" id="hospitalName" style="width:360" value="<?php echo $hospitalName; ?>" /></td>
  </tr>
  <tr align="center" bgcolor="#B0E2FF">
    <td width="200" height="30">医院地址</td>
    <td colspan="2"><input name="address" type="text" id="address"  style="width:360" value="<?php echo $address; ?>" /></td>
  </tr>
  <tr align="center" bgcolor="#B0E2FF">
    <td width="200" height="30">医院联系电话</td>
    <td colspan="2"><input name="tel" type="text" id="tel"  style="width:360" value="<?php echo $tel; ?>" /></td>
  </tr>
  <tr align="center" bgcolor="#B0E2FF">
    <td width="200" height="30">密码</td>
    <td width="200"><input type="password" name="pwd1" id="pwd1" /></td>
    <td width="200">(6-20位,不更改请留空)</td>
  </tr>
  <tr align="center" bgcolor="#B0E2FF">
    <td width="200" height="30">确认密码</td>
    <td width="200"><input type="password" name="pwd2" id="pwd2" /></td>
    <td width="200">&nbsp;</td>
  </tr>
  <tr  bgcolor="#4F94CD"  align="center">
    <td width="200" height="36" colspan="3" ><input name="edit" type="submit" value="提交" style="width:78px"/></td>
  </tr>
  </table>
</form>
<script language="javascript">
function CheckPost() {
    if (form1.pwd2.value != form1.pwd1.value) {
        alert("两次输入密码不一致");
        form1.pwd1.focus();
        return false;
    }
    if (form1.loginName.value == "") {
        alert("请输入医院管理员账号。");
        form1.loginName.focus();
        return false;
    }
    if (form1.hospName.value == "") {
        alert("请输入医院名称。");
        form1.hospName.focus();
        return false;
    }
    
    var re=/[A-Za-z0-9]+/;
    if (!re.test(form1.loginName.value)) {
        alert("管理员账户名只能为数字、字母或两者的组合。");
        form1.loginName.focus();
        return false;
    }
    return true;
}
</script>
</body>
</html>