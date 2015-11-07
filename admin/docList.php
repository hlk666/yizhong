<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'function.php';

session_start();
checkHospitalAdminLogin();

$hospitalId = $_SESSION['hospital'];
$doctors = Dbi::getDbi()->getDoctorList($hospitalId);
if (VALUE_DB_ERROR === $doctors) {
    echo '访问数据错误，请重试或者联系系统负责人。';
    exit;
}
if (empty($doctors)) {
    echo '本院现在没有医生用户。';
    exit;
}
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>医生列表</title>
<style type="text/css">
BODY {margin: 1px}
#scroll_table{ height:100%; overflow:auto;}
table{border-collapse:collapse; }
table thead{background-color:#FFFFFF}
th,td{border:1px solid #CCC}
#thead{ position:fixed; z-index:100;background-color:#FFF}
</style>
<body>
<div style="width: 100%"  align="center">
<?php
$rows = 20;
$total = count($doctors);
$page = isset($_GET['page']) ? $_GET['page'] : null;
$ret = getPaging($total, $rows, $_SERVER['REQUEST_URI'], $page);
$offset = $ret['offset'];
$navigation = $ret['navigation'];

echo $navigation;
if ($total > $rows) {
    $doctors = Dbi::getDbi()->getDoctorList($hospitalId, $offset, $rows);
}

echo "<table style='width:400px;' id='data_table' >
<tr bgcolor=#555555><td align='center'>用户名</td><td align='center'>姓名</td></tr>";
foreach ($doctors as $index => $doctor) {
    if ($index % 2 == 1) {
        $color = '#E5E5E5';
    } else {
        $color = '';
    }
    $doctorId = $doctor['account_id'];
    $loginName = $doctor['login_name'];
    $doctorName = $doctor['doctor_name'];
    echo "<tr bgcolor=$color>
    <td><div align='center' style='width:200px; height:20px'>
    <a href = 'editDoc.php?id=$doctorId'>$loginName</div></td>
    <td><div align='center' style='width:200px; height:20px'>$doctorName</div></td></tr>";
}
?>
</table>
</div>
</body>
</html>