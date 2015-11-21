<?php
require '../common.php';
include_head('病人列表');

session_start();
checkHospitalAdminLogin();

$hospitalId = $_SESSION['hospital'];

$where = ' regist_hospital_id = ' .$hospitalId;
if (isset($_POST['search'])) {
    $type = $_POST['type'];
    $value = $_POST['value'];
    if ($type == 'birth_year') {
        $value = date('Y') - $value;
    }
    if ($type == 'sex') {
        $value = $value == '男' ? 1 : 2;
    }
    $where .= " and $type = \"$value\"";
}

$patients = Dbi::getDbi()->getPatientListDistinct($where);
$total = count($patients);
if (VALUE_DB_ERROR === $total) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if ($total == 0) {
    user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
}

$rows = 20;
$page = isset($_GET['page']) ? $_GET['page'] : null;
$ret = getPaging($total, $rows, $_SERVER['REQUEST_URI'], $page);
$offset = $ret['offset'];
$navigation = $ret['navigation'];

if ($total > $rows) {
    $patients = Dbi::getDbi()->getPatientListDistinct($where, $offset, $rows);
    if (VALUE_DB_ERROR === $patients) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
    }
}
?>
<style type="text/css">
body {margin: 1px}
table{border-collapse:collapse;}
th,td{border:1px solid #CCC}
</style>
<body style="font-size:18px;">
<div style="width:100%;margin-top:20px;" align="center">
<form action="" method="post" name="search_form" id="search_form">
<div style="margin-top:15px;height:25px;vertical-align:middle;">
<span>查询条件</span>
  <select name="type" style="font-size:14px;width:80px;">
    <option value="real_name">姓名</option>
    <option value="sex">性别</option>
    <option value="birth_year">年龄</option>
    <option value="tel">电话</option>
  </select>
  <span style="margin-left:60px;">查询内容</span>
  <input name="value" type="text" style="width: 100px" />
  <input name="search" type="submit" value="查询" />
</div>
<table style="border:1px;margin-top:5px;">
    <?php echo "<div style='margin-top:30px;'>$navigation</div>";?>
        <tr bgcolor=#A3C7DF>
        <th style="width:60px;">ID</th>
        <th style="width:100px;">姓名</th>
        <th style="width:50px;">性别</th>
        <th style="width:50px;">年龄</th>
        <th style="width:90px;">编辑信息</th>
        </tr>
<?php
foreach ($patients as $index => $patient) {
    if ($index % 2 == 0) {
        $color = '#E5E5E5';
    } else {
        $color = '';
    }
    $age = date('Y') - $patient['birth_year'];
    $sex = $patient['sex'] == 1 ? '男' : '女';
    $id = $patient['patient_id'];
    $name = $patient['patient_name'];
    echo "<tr bgcolor=$color>
        <td><div align='center' style='height:19px'>$id</div></td>
        <td><div align='center' style='height:19px'>$name</div></td>
        <td><div align='center' style='height:19px'>$sex</div></td>
        <td><div align='center' style='height:19px'>$age</div></td>
        <td><div align='center' style='width:120px; height:19px'>
            <a href ='edit_patient.php?id=$id'>编辑</a></div></td>
        </tr>";
}
?>
</table></form></div>
<script type="text/javascript">
var win = null;
function NewWindow(mypage,myname,w,h,scroll) {
    LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
    TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
    settings ='height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable';
    win = window.open(mypage,myname,settings);
    win.focus();
}
</script>
</body>
</html>