<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';

$guardianId = $_GET["id"];
$oldResult = Dbi::getDbi()->getGuardianResult($guardianId);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>修改报告</title>
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
.Tab{ border-collapse:collapse; }
.Tab td{ border:solid 1px #0000EE}
-->
</style>
</head>
<body>
<?php
if (isset($_POST['edit']) && $_POST['edit']){
    if (trim($_POST['result']) == '') {
        echo "<script LANGUAGE='javascript'>alert('诊断内容不能为空！');history.back();</script>";
        exit;
    }
    else {
        $newResult = $_POST['result'];
        Dbi::getDbi()->editGuardianResult($guardianId, $newResult);
        header("location:illsum.php?id=$guardianId");
        exit;
    }
}
?>
<form name="" action="" method="post">
<table width="100%" height="100%" cellspacing="0" class="Tab"  align="center">
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
