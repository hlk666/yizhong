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
        user_goto('请输入用户名。', GOTO_FLAG_BACK);
    }
    if (empty($pwd)) {
        user_goto('请输入密码。', GOTO_FLAG_BACK);
    }

    $pwd = md5($pwd);
    $ret = DbiAdmin::getDbi()->getAdminAcount($user);
    if (VALUE_DB_ERROR === $ret) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_URL, 'index.php');
    }
    if (empty($ret)) {
        header('location:index.php?error=用户名错误。');
    } elseif ($ret['password'] != $pwd) {
        header('location:index.php?error=密码错误。');
    } else {
        $_SESSION['login'] = true;
        header('location:summary.php');
    }
    exit;
} else {
    if (isset($_SESSION['login']) && true === $_SESSION['login']) {
        header('location:summary.php');
        exit;
    }
    $message = isset($_GET['error']) ? $_GET['error'] :null;
    if (null !== $message) {
        $message = '<lable><font color="red">' . $message . '</font></lable>';
    }
    echo <<<EOF
  <form class="form-signin" method="post">$message
    <input type="text" name="user" class="form-control" style="margin-bottom:10px;" placeholder="请输入用户名" required autofocus>
    <input type="password" name="password" class="form-control" style="margin-bottom:10px;" placeholder="请输入密码" required>
    <button class="btn btn-lg btn-primary btn-block" name="login" type="submit">登录</button>
  </form>
EOF;
}

require 'tpl/footer.tpl';
