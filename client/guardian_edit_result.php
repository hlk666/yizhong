<?php
require '../common.php';
include_head('修改报告');

$guardianId = $_GET["id"];
$ret = Dbi::getDbi()->getGuardianById($guardianId);
if (VALUE_DB_ERROR === $ret) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
}
$oldResult = empty($ret) ? '' : $ret['guardian_result'];
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
td{ border:solid 1px #0000EE}
-->
</style>
<body>
<?php
if (isset($_POST['edit']) && $_POST['edit']){
    if (trim($_POST['result']) == '') {
        user_goto('诊断内容不能为空。', GOTO_FLAG_BACK);
    }
    else {
        $newResult = $_POST['result'];
        Dbi::getDbi()->flowGuardianEditResult($guardianId, $newResult);
        user_goto(null, GOTO_FLAG_URL, 'guardian_result.php?id=' . $guardianId);
    }
}
?>
<form name="" action="" method="post">
<table style="width:100%;height:100%;align:center;">
  <tr bgcolor="#4F94CD" height="10%" >
    <td colspan="2"><span class="STYLE4">&nbsp;&nbsp;修改病情总结</span></td>
  </tr>
  <tr height="80%" class="STYLE3">
    <td colspan="2" align="center">
    <textarea name="result" id="result" cols="40" rows="5" ><?php echo $oldResult;?></textarea>
    </td>
  </tr>
  <tr class="STYLE3" height="10%">
    <td  colspan="2"  align="center" >
      <input type="submit" name="edit" value="提  交"  style="width:100px"/>&nbsp;&nbsp;
      <input type="reset" name="clear" value="重  置"  style="width:100px"/> </td>
  </tr>
</table>
</form>
</body>
</html>
