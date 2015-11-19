<?php
require '../common.php';
include_head('历史心电');

$guardianId = $_GET["id"];
$diagnosis = Dbi::getDbi()->getDiagnosisByGuardian($guardianId);
if (VALUE_DB_ERROR === $diagnosis) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if (empty($diagnosis)) {
    user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
} 
?>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<table style="font-size:14px;border:0;background-color:#A3C7DF">
    <tr bgcolor='#ECEADB' style='height:30px' align='center'>
    <td width='40%'>诊断时间</td>
    <td width='*'>诊断结论</td>
    <td width='*'>诊断医生</td>
  </tr>
<?php
foreach ($diagnosis as $index => $row) {
    if ($index % 2 == 0) {
        $color = '#EBF5FF';
    } else {
        $color = '#C7E5FF';
    }
    echo"<tr bgcolor=$color align='center' style='height:25px'>
    <td>" . $row['diagnose_time'] . "</td>
    <td>" . $row['content'] . "</td>
    <td>" . $row['doctor_name'] . "</td>
    </tr>";
}
?>
</table>
</body>
</html>