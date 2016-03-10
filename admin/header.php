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
      <div style="margin-top:10px;margin-bottom:10px;font-size:x-large;text-align:center;">$title</div>
EOF;
} else {
    echo <<<EOF
      <div class="col-sm-2 blog-main">
        <ul class="nav nav-sidebar">
          <li><a href="summary.php">统计信息</a></li>
        </ul>
        <ul class="nav nav-sidebar">
          <li><a href="hospital.php">医院列表</a></li>
          <li><a href="add_hospital.php">添加医院</a></li>
        </ul>
        <ul class="nav nav-sidebar">
          <li><a href="device.php">设备列表</a></li>
          <li><a href="add_device.php">添加设备</a></li>
        </ul>
        <ul class="nav nav-sidebar">
          <li><a href="logout.php">注销登录</a></li>
        </ul>
      </div>
    <div class="col-sm-10 blog-sidebar">
      <div style="margin-top:10px;margin-bottom:10px;font-size:x-large;text-align:center;">$title</div>
EOF;
}
