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
$rows = 8;
$page = isset($_GET['page']) ? $_GET['page'] : null;
$ret = getPaging($total, $rows, $_SERVER['REQUEST_URI'], $page);
$offset = $ret['offset'];
$navigation = $ret['navigation'];
$sortNo = $total - $offset;
echo $navigation;
if ($total > $rows) {
    $ecgData = Dbi::getDbi()->getEcg($guardianId, $offset, $rows);
    if (VALUE_DB_ERROR === $ecgData) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_EXIT);
    }
}
?>
<body>
<table style="width:100%;font-size:14px;border:0;background-color:#A3C7DF">
  <tr bgcolor='#ECEADB' style='height:30px' align='center'>
  <th style='display:none;'>编号</th>
  <th width='20%'>序号</th>
  <th width='80%'>报警时间</th>
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
    echo"<tr bgcolor=$color align='center' style='height:25px'>
        <td style='display:none;'>" . $row['ecg_id'] . "</td>
        <td>" . $sortNo-- . "</td>
        <td><div align='center' style='width:150px'>" . $row['create_time'] . "</div></td>
        <td style='display:none;'>" . $row['data_path'] . "</td>
        </tr>";
}
?>
</table>
<?php include_js_file();?>
<script type="text/javascript">
$(function(){
    $("tr").dblclick(function sendURL(){
        var text = $(this).children('td').eq(0).text();
        var time = $(this).children('td').eq(2).text();
        var url= $(this).children('td').eq(3).text();
            url=$.trim(url);
            time=$.trim(time);
        window.ecg.ShowECG(url,text,time);
        
        window.location= "guardian_read_ecg.php?id="+ text; 
    });
})
</script>  
</body>
</html>