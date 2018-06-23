<?php
session_start();

if (false === stripos($_SERVER['REQUEST_URI'], 'index.php')) {
    if (!isset($_SESSION['login']) || true !== $_SESSION['login']) {
        echo 'Permission denied!';
        exit;
    }
}
?>
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>羿中医疗科技有限公司管理系统</title>
    
    <link href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap-theme.min.css"> -->
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <style type='text/css'>
      body {background-color: #CCC;}
      .nav-sidebar {margin-bottom:15px;}
    </style>
  </head>
  <body>
    <div class="container" style="background-color:#FFF">
    <div class="row">
<?php
if (isset($isHideSider) && true === $isHideSider) {
    echo <<<EOF
    <div class="col-sm-12 blog-sidebar">
      <div style="margin-top:10px;margin-bottom:10px;text-align:center;"><h2>$title</h2></div>
EOF;
} else {
    echo <<<EOF
      <div class="col-sm-2 blog-main" style="font-size:18px;">
        <ul class="nav nav-sidebar">
          <li><a href="summary.php">前 日 统 计 信 息</a></li>
          <li><a href="qps.php"> 24 小 时 并 发</a></li>
        </ul>
        <ul class="nav nav-sidebar">
          <li><a href="hospital.php">医 院 基 本 信 息</a></li>
          <li><a href="add_hospital.php">添 加 新 的 医 院</a></li>
        </ul>
        <ul class="nav nav-sidebar">
          <li><a href="device.php">设 备 基 本 信 息</a></li>
          <li><a href="add_device.php">绑 定 新 的 设 备</a></li>
          <li><a href="delivery.php">发货</a></li>
          <!--<li><a href="app_set_update.php">更 新 设 备 版 本</a></li>-->
        </ul>
        <ul class="nav nav-sidebar">
          <li><a href="hospital_device.php">医院开单数据</a></li>
          <li><a href="salesman_data.php">查询业务员开单</a></li>
        </ul>
        <ul class="nav nav-sidebar">
          <li><a href="remote_check_log.php">远程查房跟踪</a></li>
          <li><a href="invoice.php">发票开单日期</a></li>
        </ul>
        <ul class="nav nav-sidebar">
          <li><a href="logout.php">注 销 本 次 登 录</a></li>
        </ul>
      </div>
    <div class="col-sm-10 blog-sidebar">
      <div style="margin-top:10px;margin-bottom:10px;font-size:x-large;text-align:center;"><h2>$title</h2></div>
EOF;
}
