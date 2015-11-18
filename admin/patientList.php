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
BODY {margin: 1px}
#scroll_table{ height:100%; overflow:auto;}
table{border-collapse:collapse; }
table thead{background-color:#FFFFFF}
th,td{border:1px solid #CCC}
#thead{ position:fixed; z-index:100;background-color:#FFF}
.w_140{ width:140px;}
.w_145{ width:145px;}
.w_70{ width:70px;}
.w_40{ width:40px;}
.w_80{ width:80px;}
</style>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<table width="460"  border="1" bordercolor="#000000" align="center" cellspacing="0">
  <tr>
    <td height="35" bgcolor="#4F94CD"><div align="center">用户查询</div></td>
  </tr>
  <tr>
    <td height="30px" bgcolor="#B0E2FF">
    <form action="" method="post" name="search_form" id="search_form">
    <div style="margin-left:5px;margin-top:10px;height:15px;">
查询条件
      <select name="type" size="1">
        <option value="real_name">姓名</option>
        <option value="sex">性别</option>
        <option value="birth_year">年龄</option>
        <option value="tel">电话</option>
      </select>
      <span style="margin-left:60px;">查询内容</span>
      <input name="value" type="text" style="width: 100px" />
      <input name="search" type="submit" value="查询" /></div>
    </form>
    </td>
  </tr>
</table>
<table width="460"  border="1" bordercolor="#000000" align="center" cellspacing="0">
  <tr>
    <td align="center" height="500">
    <div id='scroll_table' style="height:480px;">
    <?php echo "<div>$navigation</div>";?>
    <table id='data_table' style='font-size:14px;'>
        <tr bgcolor=#555555>
        <th class='w_140'>用户编码</th>
        <th class='w_70'>姓名</th>
        <th class='w_40'>性别</th>
        <th class='w_40'>年龄</th>
        <th class='w_80'>历史心电</th>
        <th class='w_80'>健康档案</th>
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
    echo "<tbody><tr bgcolor=$color>
        <td><div align='center' style='width:200px; height:19px'>".$patient['patient_id']."</div></td>
        <td><div align='center' style='width:100px; height:19px'>".$patient['patient_name']."</div></td>
        <td><div align='center' style='width:80px; height:19px'>$sex</div></td>
        <td><div align='center' style='width:80px; height:19px'>$age</div></td>
        <td><div align='center' style='width:120px; height:19px'>
            <a href='historys.php?id=" . $patient['patient_id'] . "'>查看</a></div></td>
        <td><div align='center' style='width:120px; height:19px'>
            <a href ='editUser.php?id=" . $patient['patient_id'] . "'>查看</a></div></td>
        </tr></tbody>";
}
?>
    </table></div>  
    </td>
  </tr>
</table>
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