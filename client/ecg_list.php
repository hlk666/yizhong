<?php
require '../common.php';
include_head('报警心电');

$guardianId = $_GET["id"];
$ecgData = Dbi::getDbi()->getEcg($guardianId);
if (VALUE_DB_ERROR === $ecgData) {
    user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
}
if (empty($ecgData)) {
    user_goto(MESSAGE_DB_NO_DATA, GOTO_FLAG_EXIT);
}
$total = count($ecgData);
$rows = get_rows_by_resolution($_GET['height'], 2);
$page = isset($_GET['page']) ? $_GET['page'] : null;
$ret = getPaging($total, $rows, $_SERVER['REQUEST_URI'], $page);
$offset = $ret['offset'];
$navigation = $ret['navigation'];
$sortNo = $total - $offset;
if ($total > $rows) {
    $ecgData = Dbi::getDbi()->getEcg($guardianId, $offset, $rows);
    if (VALUE_DB_ERROR === $ecgData) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
    }
}
?>
<body topmargin="1" leftmargin="1" marginwidth="0" marginheight="0">
<div style="height:20px;margin-top:1px;"><?php echo $navigation; ?></div>
<table style="width:100%;font-size:14px;border:0;background-color:#A3C7DF">
  <tr bgcolor='#ECEADB' style='height:22px' align='center'>
  <th style='display:none;'>编号</th>
  <th width='30%'>做标记(置顶)</th>
  <th width='20%'>序号</th>
  <th width='50%'>报警时间</th>
  <th style='display:none;'>路径</th>
</tr>
<?php
foreach ($ecgData as $index => $row) {
    if ($index % 2 == 0) {
        $color = '#EBF5FF';
    } else {
        $color = '#C7E5FF';
    }
    if ($row['read_status'] == 0) {
        $color = '#FFCC00';
    }
    $ecgId = $row['ecg_id'];
    $checked = $row['mark'] == 1 ? "checked='checked'" : '';
    echo"<tr bgcolor=$color align='center' style='height:21px'>
        <td style='display:none;'>$ecgId</td>
        <td><input type='checkbox' name='chk$ecgId' onclick='mark(this, $ecgId)' $checked /></td>
        <td>" . $sortNo-- . "</td>
        <td><div align='center' style='width:150px'>" . $row['create_time'] . "</div></td>
        <td style='display:none;'>" . $row['data_path'] . "</td>
        </tr>";
}
?>
</table>
<?php include_js_file();?>
<script type="text/javascript">
function mark(chk, id) {
    var request = new XMLHttpRequest();
    var url = "<?php echo URL_ROOT . 'client/mark.php?type=e&id='; ?>" + id;
    if (chk.checked) {
        url += "&check=1";
    } else {
        url += "&check=0";
    }
    request.open("GET", url);
    request.send(null);
}
$(function(){
    $("tr").dblclick(function sendURL(){
        var text = $(this).children('td').eq(0).text();
        var time = $(this).children('td').eq(3).text();
        var url= $(this).children('td').eq(4).text();
            url=$.trim(url);
            time=$.trim(time);
        window.ecg.ShowECG(url,text,time);
        
        window.location= "guardian_read_ecg.php?id="+ text; 
    });
})
</script>
</body>
</html>