<?php
require_once 'config.php';
require_once 'Dbi.php';

session_start();
if (isset($_POST['search'])){
    $startTime = (isset($_POST['start_time']) && !empty($_POST['start_time'])) ? $_POST['start_time'] : null;
    $endTime = (isset($_POST['end_time']) && !empty($_POST['end_time'])) ? $_POST['end_time'] : null;
    $hospitalList = Dbi::getDbi()->getHospital($startTime, $endTime);
    $htmlHospital = '<tbody>';
    foreach ($hospitalList as $hospital) {
        $htmlHospital .= '<tr><td>' . $hospital['hospital_id'] . '</td><td>' 
                . $hospital['hospital_name'] . '</td><td>' 
                . $hospital['count'] . '</td><td>' 
                . '<button type="button" class="btn btn-xs btn-info" onclick="javascript:detail(' 
                        . $hospital['hospital_id'] . ',\'' 
                        . (empty($startTime) ? '' : $startTime) . '\',\'' 
                        . (empty($endTime) ? '' : $endTime) . '\')">查看</button></td></tr>';
    }
    $htmlHospital .= '</tbody>';
} else {
    $startTime = '';
    $endTime = '';
    $htmlHospital = '';
}
    
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

<div style="margin: 10 0 0 10;">
<form method="post" action="">
<label>设置查询时间范围</label>
<label>开始时间(例:2017-04-05)</label><input type="text" name="start_time" value="$startTime" />
<label>结束时间(例:2017-04-06)</label><input type="text" name="end_time" value="$endTime" />
<button type="submit" class="btn btn-sm btn-info" name="search">查找</button>
</form>
</div>

<table class="table table-bordered" style="margin-top:10px;">
<thead><tr><th>ID</th><th>医院</th><th>数量</th><th></th></tr></thead>
$htmlHospital
</table>
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
