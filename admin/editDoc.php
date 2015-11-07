<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'function.php';

session_start();
checkHospitalAdminLogin();

if(isset($_POST['submitType']) && $_POST['submitType'] == 'edit'){
    $doctorId = $_POST['doctorId'];
    $oldName = $_POST['oldName'];
    $newName = $_POST['name'];
    $oldLoginName = $_POST['oldLoginName'];
    $newLoginName = $_POST['newJobNo'];
    
    $data = array();
    
    if ($oldLoginName != $newLoginName) {
        $isExisted = Dbi::getDbi()->existData('account', ['login_name' => $newLoginName]);
        if ($isExisted) {
            echo "<script language='javascript'>alert('该登录名已被他人使用。');history.back();</script>";
            exit;
        }
        
        $data['login_name'] = $newLoginName;
    }
    
    if ($oldName != $newName) {
        $data['real_name'] = $newName;
    }
    
    if (!empty($_POST['psw1'])) {
        $data['password'] = md5($_POST['psw1']);
    }
    
    if (empty($data)) {
        echo "<script language='javascript'>alert('没有修改任何信息，请不要提交。');history.back();</script>";
        exit;
    }
    $ret = Dbi::getDbi()->editAccount($doctorId, $data);
    if (VALUE_DB_ERROR === $ret) {
        echo "<script language='javascript'>alert('操作失败，请重试。');history.back();</script>";
    } else {
        echo "<script language='javascript'>alert('修改成功。');window.location.href='docList.php'</script>";
    }
    exit;
}
 
if(isset($_POST['submitType']) && $_POST['submitType'] == 'delete'){
    $ret = Dbi::getDbi()->delDoctor($_POST['doctorId']);
    if (VALUE_DB_ERROR === $ret) {
        echo "<script language='javascript'>alert('操作失败，请重试。');history.back();</script>";
    } else {
        echo "<script language='javascript'>alert('删除成功。');window.location.href='docList.php'</script>";
    }
    exit;
}

$doctorId = $_GET['id'];
if (empty($doctorId)) {
    echo '错误访问。';
    exit;
}
$info = Dbi::getDbi()->getDoctorInfo($doctorId);
$ret = checkDataFromDB($info);
if ($ret !== true) {
    echo $ret;
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理医生</title>
</head>
<body>
<form method="post" name="myform" action="" onsubmit="return false;">
<input type="hidden" name="submitType" value="" />
<input type="hidden" name="doctorId" value="<?php echo $info['doctor_id']; ?>" />
<input type="hidden" name="oldLoginName" value="<?php echo $info['login_name']; ?>" />
<input type="hidden" name="oldName" value="<?php echo $info['doctor_name']; ?>" />
<table style="height:221px;width:500px;" align="center">
<tr bgcolor="#4F94CD"><td height="36" colspan="3"  >&nbsp;</td></tr>
<tr align="center" bgcolor="#B0E2FF">
  <td width="134" ><strong>姓名:</strong></td>
  <td width="165" ><?php echo $info['doctor_name'];?></td>
  <td width="285" ><input name="name" type="text" style="width: 179px" value="<?php echo $info['doctor_name']; ?>"/></td>
</tr>
<tr align="center" bgcolor="#B0E2FF">
  <td height="32" ><strong>登录用的名字:</strong></td>
  <td ><?php echo $info['login_name'];?></td>
  <td ><input name="newJobNo" type="text" style="width: 179px" value="<?php echo $info['login_name']; ?>"/></td>
</tr>
<tr align="center" bgcolor="#B0E2FF">
  <td height="30" ><strong>新密码:</strong></td>
  <td > （不更改请留空）</td>
  <td ><input name="psw1" type="password" style="width: 179px" value="" /></td>
</tr>
<tr align="center" bgcolor="#B0E2FF">
  <td height="30" ><strong>确认新密码：</strong></td>
  <td >（不更改请留空）</td>
  <td ><input name="psw2" type="password" style="width: 179px" value="" /></td>
</tr>
<tr align="center" bgcolor="#4F94CD">
  <td colspan="3" >
  <input name="edit" type="submit" value="确认修改" style="width:78px" onclick="checkPost()" />
  <input name="delete" type="submit" value="删除帐号" style="margin-left:100px;width:78px;" onclick="myDelete()" />
  </td>
</tr>
</table>
</form>
<script language="javascript">
function myDelete() {
    myform.submitType.value="delete";
    myform.submit();
}
function checkPost() {
    if (myform.newJobNo.value == "") {
        alert("登录用的名字不能为空。");
        myform.newJobNo.focus();
        return false;
    }
    if (myform.name.value == "") {
        alert("姓名不能为空。");
        myform.name.focus();
        return false;
    }
    var set =/[^\u4e00-\u9fa5]/;
    if (set.test(myform.name.value)) {
        alert("请输入中文姓名。");
        myform.name.focus();
        return false;
    }
    if (myform.name.value.length > 50) {
        alert("输入的姓名过长。");
        myform.name.focus();
        return false;
    }
    if (myform.name.value.length < 2) {
        alert("输入的姓名过短。");
        myform.name.focus();
        return false;
    }
    if (myform.psw1.value != null && myform.psw1.value != "") {
        if (myform.psw2.value != form1.psw1.value) {
            alert("两次输入密码不一致。");
            myform.psw1.focus();
            return false;
        }
    }
    if (myform.newJobNo.value==myform.oldLoginName.value 
            && myform.name.value==myform.oldName.value 
            && (myform.psw1.value == null || myform.psw1.value == "")) {
            alert("您什么都没有修改，不能提交。");
            return false;
    }
    myform.submitType.value="edit";
    myform.submit();
}
</script>
</body>
</html>