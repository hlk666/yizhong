<?php
require '../common.php';
include_head('医生列表');

session_start();
checkHospitalAdminLogin();

$hospitalId = $_SESSION['hospital'];
$doctors = Dbi::getDbi()->getDoctorList($hospitalId);
if (VALUE_DB_ERROR === $doctors) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if (empty($doctors)) {
    user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
}
?>
<style type="text/css">
BODY {margin: 1px}
#scroll_table{ height:100%; overflow:auto;}
table{border-collapse:collapse;}
table thead{background-color:#FFFFFF}
th,td{border:1px solid #CCC}
#thead{ position:fixed; z-index:100;background-color:#FFF}
</style>
<body>
<div style="width: 100%" align="center">
<table style='width:400px;' id='data_table' >
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
    if (VALUE_DB_ERROR === $doctors) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
    }
}

echo "<tr bgcolor=#555555><td align='center'>用户名</td><td align='center'>姓名</td></tr>";
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
    <a href = 'edit_doctor.php?id=$doctorId'>$loginName</div></td>
    <td><div align='center' style='width:200px; height:20px'>$doctorName</div></td></tr>";
}
?>
</table>
</div>
</body>
</html>