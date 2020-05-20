<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '登录页';
$isHideSider = true;
require 'header.php';

if (isset($_POST['login'])) {
    $user = !isset($_POST['user']) ? null : $_POST['user'];
    $pwd = !isset($_POST['password']) ? null : $_POST['password'];
    
    if (empty($user)) {
        user_back_after_delay('请输入用户名。');
    }
    if (empty($pwd)) {
        user_back_after_delay('请输入密码。');
    }

    $pwd = md5($pwd);
    $ret = DbiAdmin::getDbi()->getAdminAcount($user);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    if (empty($ret)) {
        header('location:index.php?error=用户名错误。');
    } elseif ($ret['password'] != $pwd) {
        header('location:index.php?error=密码错误。');
    } else {
        $_SESSION['login'] = true;
        $_SESSION['user'] = $_POST['user'];
        $_SESSION['user_id'] = $ret['account_id'];
        if ($ret['type'] == '0') {
            header('location:hospital.php');
        } else {
            header('location:add.php');
        }
    }
    exit;
} else {
    /*
    if (isset($_SESSION['login']) && true === $_SESSION['login']) {
        if ($_SESSION['user'] == 'admin') {
            header('location:summary.php');
        } else {
            header('location:add.php');
        }
        
        exit;
    }*/
    $message = isset($_GET['error']) ? $_GET['error'] :null;
    if (null !== $message) {
        $message = '<lable><font color="red">' . $message . '</font></lable>';
    }
    echo <<<EOF
  <form class="form-signin" method="post">$message
    <input type="text" name="user" class="form-control" style="margin-bottom:10px;" placeholder="请输入用户名" required autofocus>
    <input type="password" name="password" class="form-control" style="margin-bottom:10px;" placeholder="请输入密码" required>
    <!--<div class="checkbox"><label><input type="checkbox" name="bind">绑定设备(不选则是添加医院)</label></div>-->
    <button class="btn btn-lg btn-primary btn-block" name="login" type="submit">登录</button>
  </form>
EOF;
}

require 'tpl/footer.tpl';
