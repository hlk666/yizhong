<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';

$hospitalId = $_GET['hospital'];
$ecgId = $_GET['eid'];
if (isset($_POST['apply']) && $_POST['apply']){
    $message = $_POST['message'];
    $responseHospital = $_POST['response_hospital'];
    $ret = Dbi::getDbi()->addConsultation($hospitalId, $responseHospital, $ecgId, $message);
    if (VALUE_DB_ERROR == $ret) {
        echo '会诊请求发送失败，请重试或联系管理员。';
    } else {
        echo '会诊请求发送成功。';
    }
    exit;
}

$parentHospital = Dbi::getDbi()->getParentHospitals($hospitalId);
if (VALUE_DB_ERROR == $parentHospital) {
    echo '读取上级医院信息失败，请重试或联系管理员。';
    exit;
}
if (empty($parentHospital)) {
    echo '本院暂时没有上级医院，请添加上级医院后再申请会诊。';
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>诊断报告</title>
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
.STYLE5 {font-size: 14px}
.Tab{ border-collapse:collapse; width:300px; height:300px;}
.Tab td{ border:solid 1px #0000EE}
-->
</style>
</head>
<body>
<form name="" action="" method="post">
<table cellspacing="0" class="Tab" align="center">
  <tr bgcolor="#4F94CD"><td height="30" colspan="2"><strong>&nbsp;&nbsp;医院列表</strong></td></tr>
  <tr>
    <td height="178" colspan="2"><div style='height:185px; overflow:auto;'>
    <table width='100%' border='0' style='width:100%; margin:0;font-size:12px;' >
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