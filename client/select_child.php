<?php
require '../common.php';
include_head('选择医院');
session_start();
$hospitalId = $_GET['id'];
$child = Dbi::getDbi()->getHospitalChild($hospitalId);
if (empty($child)) {
    user_goto(MESSAGE_PARAM, GOTO_FLAG_BACK);
}
?>
<style type="text/css">
<!--
.STYLE4 {
    font-family: "宋体";
    font-weight: bold;
}
td{ border:solid 1px #0000EE}
-->
</style>
<body>
<form name="form_select_hospital" method="post">
<table style="border-collapse:collapse;width:100%;height:100%;align:center;">
  <tr bgcolor="#4F94CD">
    <td  height="29"><span class="STYLE4">&nbsp;&nbsp;选择查看医院</span></td>
  </tr>
  <tr>
    <td colspan="3"><select name="custody_hos" style="width: 100%" id="custody_hos">
    <?php
    echo '<option value="0" selected="selected"></option>';
    foreach ($child as $value) {
        echo '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
    }
    ?>
    </select></td>
  </tr>
  <tr bgcolor="#B0E2FF">
    <td align="center" >
      <input type="button" name="select" value="确定"  style="width:100px" onclick="Select()" />
      <input type="button" name="return" value="返回"  style="width:100px;margin-left:25px;" onclick="javascript:history.back();" />
    </td>
  </tr> 
</table>
</form>
<script type="text/javascript">
function Select() {
    if (form_select_hospital.custody_hos.value == "0") {
        alert("请选择下级医院。");
    } else {
        window.location.href='patient_list.php?current_flag=0&id=' + form_select_hospital.custody_hos.value;
    }
}
</script>
</body>
</html>