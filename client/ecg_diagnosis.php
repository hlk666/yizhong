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
<table style="border-collapse:collapse;border:0;font-size:14px;bgcolor:#C1BDBE">
  <tr bgcolor='#ECEADB' style='height:30px' align='center'>
    <td width='40%'>诊断时间</td>
    <td width='*'>诊断结论</td>
    <td width='15%'>诊断医生</td>
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
    <td>" . $row['content'] . "</td>
    <td>" . $row['doctor_name'] . "</td>
    </tr>";
}
?>
</table>
</body>
</html>