<?php
require '../common.php';
include_head('添加医生');

session_start();
checkHospitalAdminLogin();

$hospitalId = $_SESSION['hospital'];
$creator = $_SESSION['loginId'];

if (isset($_POST['add']) && $_POST['add']){
    $pwd = $_POST['pwd1'];
    $newPwd = $_POST['pwd2'];
    if (empty($pwd) || empty($newPwd) || $pwd != $newPwd) {
        user_goto('请正确设置密码。', GOTO_FLAG_BACK);
    }
    
    $loginName = $_POST['login_name'];
    $isExisted = Dbi::getDbi()->existedLoginName($loginName);
    if ($isExisted) {
        user_goto('该登录名已被他人使用。', GOTO_FLAG_BACK);
    }
    
    $name = $_POST['name'];
    
    $type = 2;
    $ret = Dbi::getDbi()->addAccount($loginName, $name, $pwd, $type, $hospitalId, $creator);
    if (VALUE_DB_ERROR === $ret) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
    } else {
        user_goto(MESSAGE_SUCCESS, GOTO_FLAG_URL, 'docList.php');
    }
}
?>
<body>
<table width="427" height="261" align="center" class="style18" >
<tr bgcolor="#4F94CD">
  <td width="419" height="38" colspan="2" class="style7"> &nbsp;&nbsp;<strong>您的位置</strong>：添加医生</td>
</tr>
<form name="formAddDoc" method="post" action="" onsubmit="return checkAddDoc()">
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
  <td width="419" height="49" align="center" valign="middle" style="width: 200px; height: 31px">
    <input name="add" type="submit" value="提 交" style="width:150px" />
  </td>
  <td width="200" align="center" valign="middle" style="height: 31px">
    <input name="Button1" type="reset" value="重 置" style="width:150px" />
  </td>
</tr>
</form>
</table>
<?php include_js_file();?>
</body>
</html>