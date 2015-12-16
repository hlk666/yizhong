<?php
require '../common.php';
include_head('诊断结论');

$ecgId = $_GET["id"];
$diagnosis = Dbi::getDbi()->getDiagnosisByEcg($ecgId);
if (VALUE_DB_ERROR === $diagnosis) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if (empty($diagnosis)) {
    user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
} 
?>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<table style="border:0;font-size:14px;background-color:#C1BDBE;">
  <tr bgcolor='#ECEADB' style='height:30px' align='center'>
    <td width='70px'><div style='width:70px;'>诊断时间</div></td>
    <td width='60px'><div style='width:60px;'>诊断医生</div></td>
    <td>诊断结论</td>
  </tr>
<?php
foreach ($diagnosis as $index => $row) {
    if ($index % 2 == 0) {
        $color = '#EBF5FF';
    } else {
        $color = '#C7E5FF';
    }
    echo"<tr bgcolor=$color align='center' style='height:25px'>
    <td>" . $row['create_time'] . "</td>
    <td>" . $row['doctor_name'] . "</td>
    <td>" . $row['content'] . "</td>
    </tr>";
}
?>
</table>
</body>
</html>