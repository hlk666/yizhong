<?php
require '../common.php';
include_head('管理医生');

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
        $isExisted = Dbi::getDbi()->existedLoginName($newLoginName);
        check_user_existed($isExisted);
        $data['login_name'] = $newLoginName;
    }
    
    if ($oldName != $newName) {
        $data['real_name'] = $newName;
    }
    
    if (!empty($_POST['pwd1'])) {
        $data['password'] = md5($_POST['pwd1']);
    }
    
    if (empty($data)) {
        user_goto('没有修改任何信息，请不要提交。', GOTO_FLAG_BACK);
    }
    $ret = Dbi::getDbi()->editAccount($doctorId, $data);
    if (VALUE_DB_ERROR === $ret) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
    } else {
        user_goto(MESSAGE_SUCCESS, GOTO_FLAG_URL, 'doctors.php');
    }
}
 
if(isset($_POST['submitType']) && $_POST['submitType'] == 'delete'){
    $ret = Dbi::getDbi()->delDoctor($_POST['doctorId']);
    if (VALUE_DB_ERROR === $ret) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
    } else {
        user_goto(MESSAGE_SUCCESS, GOTO_FLAG_URL, 'doctors.php');
    }
}

$doctorId = $_GET['id'];
if (empty($doctorId)) {
    user_goto(MESSAGE_PARAM, GOTO_FLAG_EXIT);
}
$info = Dbi::getDbi()->getDoctorInfo($doctorId);
if (VALUE_DB_ERROR === $info) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
?>
<body>
<form method="post" name="formEditDoc" action="" onsubmit="return false;">
<input type="hidden" name="submitType" value="" />
<input type="hidden" name="doctorId" value="<?php echo $info['doctor_id']; ?>" />
<input type="hidden" name="oldLoginName" value="<?php echo $info['login_name']; ?>" />
<input type="hidden" name="oldName" value="<?php echo $info['doctor_name']; ?>" />
<table style="height:221px;width:500px;align:center;">
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
  <td ><input name="pwd1" type="password" style="width: 179px" value="" /></td>
</tr>
<tr align="center" bgcolor="#B0E2FF">
  <td height="30" ><strong>确认新密码：</strong></td>
  <td >（不更改请留空）</td>
  <td ><input name="pwd2" type="password" style="width: 179px" value="" /></td>
</tr>
<tr align="center" bgcolor="#4F94CD">
  <td colspan="3" >
  <input name="edit" type="submit" value="确认修改" style="width:78px" onclick="checkEditDoc()" />
  <input name="delete" type="submit" value="删除帐号" style="margin-left:100px;width:78px;" onclick="deleteDoc()" />
  </td>
</tr>
</table>
</form>
<?php include_js_file();?>
</body>
</html>