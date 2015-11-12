<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>报警心电</title>
</head>
<body>
<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'function.php';

$guardianId = $_GET["id"];

$total = Dbi::getDbi()->getRecordCount('ecg', ' guardian_id = ' . $guardianId);
if ($total == VALUE_DB_ERROR) {
    echo '获取心电数据失败。';
    exit;
}
if ($total == 0) {
    echo '当前监护没有历史心电数据。';
    exit;
}
$rows = 8;
$page = isset($_GET['page']) ? $_GET['page'] : null;
$ret = getPaging($total, $rows, $_SERVER['REQUEST_URI'], $page);
$offset = $ret['offset'];
$navigation = $ret['navigation'];
$sortNo = $total - $offset;
echo $navigation;

$ecgData = Dbi::getDbi()->getEcg($guardianId);
?>
<table width='100%' style='font-size:14px;' border='0' cellpadding='0' bgcolor='#A3C7DF' >
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
<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
$(function(){
   var rgb;
    $("tr").dblclick(function sendURL(){
        var text = $(this).children('td').eq(0).text();
        var time = $(this).children('td').eq(2).text();
        var url= $(this).children('td').eq(3).text();
            url=$.trim(url);
            time=$.trim(time);
        window.ecg.ShowECG(url,text,time);
        
        window.location= "update_read_status.php?id="+ text; 
    });
   $("tr").mouseover(function(){
        rgb = $(this).css('background-color');
        $(this).css({
        'backgroundColor':'#5fafcd',
        'color':'#fff'
        });
    });
   $("tr").mouseout(function(){
       $(this).css({
        'backgroundColor':rgb,
        'color':'#000'
         });
    });
})
</script>  
</body>
</html>