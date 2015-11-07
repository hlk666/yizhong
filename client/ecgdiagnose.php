<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';

$ecgId = $_GET["id"];
$diagnosis = Dbi::getDbi()->getDiagnosisByEcg($ecgId);
if (VALUE_DB_ERROR === $diagnosis) {
    echo '查询数据时发生错误，请重试或联系管理员。';
    exit;
}
if (empty($diagnosis)) {
    echo '该条记录未做诊断。';
    exit;
} 
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>诊断结论</title>
</head>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<table style='font-size:14px;' border='0' cellpadding='0' bgcolor='#C1BDBE' >
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