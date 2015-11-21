<?php
require '../common.php';
include_head('管理病号');

session_start();
checkHospitalAdminLogin();
$patientId = $_GET['id'];

if (isset($_POST['edit'])){
    $oldName = $_POST['old_name'];
    $newName = $_POST['name'];
    $oldSex = $_POST['old_sex'];
    $sex = $_POST['sex'];
    $oldBirthYear = $_POST['old_birth_year'];
    $birthYear = $_POST['birth_year'];
    $oldTel = $_POST['old_tel'];
    $tel = $_POST['tel'];
    $oldAddress = $_POST['old_address'];
    $address = $_POST['address'];
    //set new value to array.
    $data = array();
    if ($oldName != $newName) {
        $data['patient_name'] = $newName;
    }
    if ($oldSex != $sex) {
        $data['sex'] = $sex;
    }
    if ($oldBirthYear != $birthYear) {
        $data['birth_year'] = $birthYear;
    }
    if ($oldTel != $tel) {
        $data['tel'] = $tel;
    }
    if ($oldAddress != $address) {
        $data['address'] = $address;
    }
    //if not modified, return.
    if (empty($data)) {
        user_goto(MESSAGE_NOT_EDIT, GOTO_FLAG_BACK);
    }
    //update db.
    $ret = Dbi::getDbi()->editPatient($patientId, $data);
    if (VALUE_DB_ERROR === $ret) {
        Dbi::getDbi()->rollBack();
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
    }
    user_goto(MESSAGE_SUCCESS, GOTO_FLAG_URL, 'patients.php');
}

$info = Dbi::getDbi()->getPatient($patientId);
if (VALUE_DB_ERROR === $info) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
$name = $info['patient_name'];
$sex = $info['sex'];
$birthYear = $info['birth_year'];
$tel = $info['tel'];
$address = $info['address'];
?>
<style>
tr {background-color:#B0E2FF;}
td {text-align:left;padding-left:5px;height:30px;}
</style>
<body>
<form name="formEditPatient" method="post" action="" onSubmit="return CheckEditPatient()">
<input type="hidden" name="old_name" value="<?php echo $name; ?>" />
<input type="hidden" name="old_sex" value="<?php echo $sex; ?>" />
<input type="hidden" name="old_birth_year" value="<?php echo $birthYear; ?>" />
<input type="hidden" name="old_tel" value="<?php echo $tel; ?>" />
<input type="hidden" name="old_address" value="<?php echo $address; ?>" />
<div style="width:100%;margin-top:5px;" align="center">
<table>
  <tr><td colspan="2"><div align="center"><strong>病号信息管理</strong></div></td></tr>
  <tr>
    <td width="120">姓名 </td>
    <td width="300"><input name="name" type="text" id="name" value="<?php echo $name; ?>" /></td>
  </tr>
  <tr>
    <td>性别</td>
    <td>
    <select name="sex" id="sex">
      <option value="1" <?php if($sex == "1") echo 'selected="selected"'?>>男 </option>
      <option value="2" <?php if($sex == "2") echo 'selected="selected"'?>>女 </option>
     </select>
  </tr>
  <tr>
    <td>出生年份</td>
    <td colspan="2"><input name="birth_year" type="text" id="birth_year" value="<?php echo $birthYear; ?>" /></td>
  </tr>
  <tr>
    <td>联系电话</td>
    <td><input name="tel" type="text" id="tel" value="<?php echo $tel; ?>" /></td>
  </tr>
  <tr>
    <td>地址</td>
    <td><input type="text" name="address" id="address" value="<?php echo $address; ?>" /></td>
  </tr>
  <tr><td colspan="2"><div align="center"><input name="edit" type="submit" value="修改" style="width:78px"/></div></td></tr>
  </table>
</div>
</form>
<script type="text/javascript">
function CheckEditPatient() {
    if (formEditPatient.name.value == "") {
        alert("请输入姓名。");
        formEditPatient.name.focus();
        return false;
    }
    if (formEditPatient.birth_year.value == "") {
        alert("请输入出生年份。");
        formEditPatient.birth_year.focus();
        return false;
    }
    if (formEditPatient.tel.value == "") {
        alert("请输入联系电话。");
        formEditPatient.tel.focus();
        return false;
    }
    if (formEditPatient.address.value == "") {
        alert("请输入地址。");
        formEditPatient.address.focus();
        return false;
    }
    if (formEditPatient.name.value==formEditPatient.old_name.value 
            && formEditPatient.sex.value==formEditPatient.old_sex.value
            && formEditPatient.birth_year.value==formEditPatient.old_birth_year.value 
            && formEditPatient.tel.value==formEditPatient.old_tel.value 
            && formEditPatient.address.value==formEditPatient.old_address.value) {
            alert("您什么都没有修改，不能提交。");
            return false;
    }
    return true;
}
</script>
</body>
</html>