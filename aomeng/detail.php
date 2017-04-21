<?php
require_once 'config.php';
require_once 'Dbi.php';

if (!isset($_GET['id'])) {
    echo 'ID错误。';
    exit;
}
$id = $_GET['id'];
$startTime = empty($_GET['start_time']) ? null : $_GET['start_time'];
$endTime = empty($_GET['end_time']) ? null : $_GET['end_time'];

$ret = Dbi::getDbi()->getPatient($id, $startTime, $endTime);
$count = count($ret);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$rows = 20;
$offset = ($page - 1) * $rows;
$lastPage = ceil($count / $rows);

if (1 === $page) {
    $ret = array_slice($ret, 0, $rows);
} else {
    $ret = Dbi::getDbi()->getPatient($id, $startTime, $endTime, $offset, $rows);
}

$htmlPatients = '';
foreach ($ret as $value) {
    $status = empty($value['end_time']) ? '监护中' : '已结束'; 
    $htmlPatients .= '<tr><td>' 
            . $value['id'] . '</td><td>'
            . $value['hospital_name'] . '</td><td>'
            . $value['patient_name'] . '</td><td>'
            . $value['start_time'] . '</td><td>'
            . $value['end_time'] . '</td><td>'
            . $value['device_id'] . '</td><td>'
            . $value['doctor_name'] . '</td><td>'
            . $status . '</td></tr>';
}
$currentPage = "detail.php?id=$id&start_time=$startTime&end_time=$endTime";
$paging = getPaging($page, $lastPage, $currentPage);

echo <<<EOF
<html><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>羿中医疗科技有限公司管理系统</title>
    
    <link href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="yizhong.css">
    <!-- <link rel="stylesheet" href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap-theme.min.css"> -->
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
<table class="table table-bordered" style="margin-top:10px;">
<thead>
  <tr>
    <th>ID</th>
    <th>医院</th>
    <th>姓名</th>
    <th>开始时间</th>
    <th>结束时间</th>
    <th>设备号</th>
    <th>开单医生</th>
    <th>监护状态</th>
  </tr>
</thead>
<tbody>$htmlPatients</tbody>
</table>
<div style="text-align:right;">
<ul class="pagination">$paging</ul>
<div>
<div style="text-align:center;">
<form method="post" action="index.php">
<input type="hidden" name="start_time" value="$startTime" />
<input type="hidden" name="end_time" value="$endTime" />
<button type="submit" class="btn btn-lg btn-info" name="search">返回</button>
</div>
</form>
<script type="text/javascript" src="yizhong.js"></script>
<script src="http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js"></script>
<script src="http://apps.bdimg.com/libs/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="adddate.js"></script>
<script type="text/javascript">
    $('.selectpicker').selectpicker();
</script>
</body></html>
EOF;

function getPaging($page, $lastPage, $currentPage = null) {
    if (null !== $currentPage) {
        $link = '<a href="' . $currentPage . '&page=';
    } else {
        $link = '<a href="?page=';
    }

    $paging = '<li>' . $link . '1">首页</a></li>';
    if ($page == 1) {
        $paging .= '<li class="disabled">' . $link . '1">前页</a></li>';
    } else {
        $paging .= '<li>' . $link . ($page - 1) . '">前页</a></li>';
    }
    for ($i = 1; $i <= $lastPage; $i++) {
        $paging .= '<li';
        if ($page == $i) {
            $paging .= ' class="active"';
        }
        $paging .= '>' . $link . $i . '">' . $i . '</a></li>';
    }
    if ($page == $lastPage) {
        $paging .= '<li  class="disabled">' . $link . $lastPage . '">后页</a></li>';
    } else {
        $paging .= '<li>' . $link . ($page + 1) . '">后页</a></li>';
    }
    $paging .= '<li>' . $link . $lastPage . '">尾页</a></li>';
    return $paging;
}