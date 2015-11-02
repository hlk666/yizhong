<?php
require '../config/path.php';
require '../config/value.php';
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'function.php';

$guardianId = $_GET["id"];
include ("../libraries/conn.php");
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>报警心电</title>

</head>
<body>
<?php
//@todo in future, send start_time and end_time as parameter of get instead of accessing db.
$times = Dbi::getDbi()->getGuardianTime($guardianId);
if (empty($times)) {
    echo '系统错误：没有监护时间信息。';
    exit;
}

$where = ' and guardian_id = ' . $guardianId 
        . ' and create_time >= "' . $times['start_time'] . '"';
if ($times['end_time'] != null) {
    $where .= ' and create_time <= "' . $guardian['end_time'] . '"';
}
$total = Dbi::getDbi()->getRecordCount('ecg', $where);    
if ($total == 0) {
    echo '当前用户没有历史心电数据。';
    exit;
}
$rows = 8;
$page = isset($_GET['page']) ? $_GET['page'] : null;
$ret = getPaging($total, $rows, $_SERVER['REQUEST_URI'], $page);
$offset = $ret['offset'];
$navigation = $ret['navigation'];
echo $navigation;
$where .= ' order by ecg_id desc limit ' . $offset . ', ' . $rows;
$ecgData = Dbi::getDbi()->getAllData($sql)
    echo"<table width='100%' style='font-size:14px;' border='0' cellpadding='0' bgcolor='#A3C7DF' >
     <tr bgcolor='#ECEADB' style='height:30px' align='center'>
     <th style='display:none;'>编号</th>
     <th width='20%'>序号</th>
     <th width='80%'>报警时间</th>
     <th style='display:none;'>路径</th>
     </tr>";
    $i = 1;
     while($row=mysql_fetch_array($result)){
      if ($i % 2 == 0){
       $color='#C7E5FF';
         }
    else{
         $color='#EBF5FF';
         } 
      if ($row[readstate]==0) {
        $color = '#FFCC00';
        }
         $i += 1;
    echo"<tr bgcolor=$color align='center' style='height:25px'>
        <td style='display:none;'>".$row[e_id]."</td>
     <td>".$listnum--."</td>    
    <td><div align='center' style='width:150px'>".$row[storeTime]."</div></td>
    <td style='display:none;'>".$row[path]."</td>
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
        
        window.location= "upreadstate.php?id="+ text; 
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