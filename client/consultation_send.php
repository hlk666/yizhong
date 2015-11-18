<?php
require '../common.php';
include_head('会诊请求');

if (!isset($_GET['hospital']) || !isset($_GET['eid'])) {
    user_goto(MESSAGE_PARAM, GOTO_FLAG_EXIT);
}
$hospitalId = $_GET['hospital'];
$ecgId = $_GET['eid'];
if (isset($_POST['apply']) && $_POST['apply']){
    if (!isset($_POST['message']) || !isset($_POST['response_hospital'])) {
        user_back_after_delay('请输入会诊请求信息，并且选择一个上级医院。', 1500);
    }
    $message = $_POST['message'];
    $responseHospital = $_POST['response_hospital'];
    $ret = Dbi::getDbi()->flowConsultationSend($hospitalId, $responseHospital, $ecgId, $message);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay('会诊请求发送失败，请重试或联系管理员。', 1500);
    } else {
        user_back_after_delay('会诊请求发送成功。', 1500);
    }
}

$parentHospital = Dbi::getDbi()->getHospitalParent($hospitalId);
if (VALUE_DB_ERROR === $parentHospital) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if (empty($parentHospital)) {
    user_goto('本院暂时没有上级医院，请添加上级医院后再申请会诊。', GOTO_FLAG_EXIT);
}
?>
<style type="text/css">
<!--
.STYLE3 {
    border: 1px solid #808080;
    background-color: #B0E2FF;
}
.STYLE4 {
    font-family: "宋体";
    font-weight: bold;
}
.Tab{ }
td{ border:solid 1px #0000EE}
-->
</style>
<body>
<form name="" action="" method="post">
<table style="border-collapse:collapse; width:300px; height:300px;align:center;">
  <tr bgcolor="#4F94CD"><td height="30" colspan="2"><strong>&nbsp;&nbsp;医院列表</strong></td></tr>
  <tr>
    <td height="178" colspan="2"><div style='height:185px; overflow:auto;'>
    <table style="border:0;width:100%; margin:0;font-size:12px;">
      <tr bgcolor='#666666'><th width='20'>选择</th><th width='210'>医院名称</th></tr>
<?php
foreach ($parentHospital as $index => $row) {
    if ($index % 2 == 0) {
        $color = '#E5E5E5';
    } else {
        $color = '#ADD8E6';
    }
    echo "<tr bgcolor=$color><td><div align='center' >
    <input type='radio' name='response_hospital' value=" . $row['hospital_id'] . " >
    </div></td><td><div align='center' >" . $row['hospital_name'] . "</div></td></tr>";
}
?>  
    </table></div>
    </td>
  </tr>
  <tr bgcolor="#4F94CD"><td height="30" colspan="2"><span class="STYLE4">&nbsp;&nbsp;会诊请求说明</span></td></tr>
  <tr class="STYLE3"><td height="50" colspan="2" align="center">
    <textarea name="message" cols="50" rows="5"></textarea></td>
  </tr>
  <tr class="STYLE3">
    <td colspan="2"  align="center" >
      <input type="submit" name="apply" value="提交会诊"  style="width:100px"/>&nbsp;&nbsp;
      <input type="reset" name="reset" value="重  置"  style="width:100px"/>
    </td>
  </tr>
</table>
</form>
</body>
</html>