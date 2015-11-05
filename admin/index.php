<?php

session_start();

if (isset($_SESSION["isLogin"]) && isset($_SESSION["loginType"])
        && $_SESSION["isLogin"] && $_SESSION["loginType"] == 1) {
    header("location:adminf.html");
    exit;
}

if (isset($_SESSION["isLogin"]) && isset($_SESSION["loginType"])
        && $_SESSION["isLogin"] && $_SESSION["loginType"] == 0) {
    header("location:sysf.html");
    exit;
}

if (isset($_SESSION["isLogin"]) && isset($_SESSION["loginType"])
        && $_SESSION["isLogin"] && $_SESSION["loginType"] == 2) {
    unset($_SESSION['isLogin']);
    unset($_SESSION['user']);
    echo "<script language=javascript>alert(\"您不是管理员用户，请用管理员用户登录。\");window.location='index.php'</script>";
    exit;
}

$errorMsg = '';
if(isset($_POST['user'])) {
    if(trim($_POST["user"]) == ""){
        $errorMsg = '请输入登录名。';
    } else {
        $_SESSION['user'] = $_POST['user'];
    }

    if ($errorMsg == '' && trim($_POST["pwd"]) == ""){
        $errorMsg = '请输入密码。';
    }

    if ($errorMsg == '') {
        include '../config/path.php';
        include '../config/value.php';
        include PATH_LIB . 'Dbi.php';

        $user = $_POST['user'];
        $pwd = md5($_POST['pwd']);

        $ret = Dbi::getDbi()->getAcount($user);
        if (empty($ret)) {
            $errorMsg = '您输入的用户不存在。';
        } elseif ($ret['password'] != $pwd) {
            $errorMsg = '您输入的密码有误。';
        } else {
            $accountId = $ret['account_id'];
            $loginType = $ret['type'];
            $hospitalId = $ret['hospital_id'];
            
            if ($loginType > 1) {
                unset($_SESSION['isLogin']);
                unset($_SESSION['user']);
                echo "<script language=javascript>alert(\"您不是管理员用户，请用管理员用户登录。\");window.location='index.php'</script>";
                exit;
            }

            setcookie(session_name(), session_id(), time() + 8 * 3600, "/");

            $_SESSION['isLogin'] = true;
            $_SESSION['loginType'] = $loginType;
            $_SESSION['hospital'] = $hospitalId;
            $_SESSION["loginId"] = $accountId;
            unset($_SESSION['user']);

            if($loginType == 0) {
                header("location:sysf.html");
            }
            if ($loginType == 1) {
                header("location:adminf.html");
            }
            
            exit;
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理系统首页</title>
<link href="../style/public.css" rel="stylesheet" type="text/css" />
<!--IE6透明判断-->
<!--[if IE 6]>
<script src="js/DD_belatedPNG_0.0.8a-min.js"></script>
<script>
DD_belatedPNG.fix('.logo,.denglukuang,.line');
</script>
<![endif]-->
<style type="text/css">
<!--
span{ color: #FF0000;}
-->
</style>
</head>
<body>
<div class="bg">
    <div class="logo"></div>
    <div class="xindian">
        <div class="flash">
      <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="1600" height="92">
        <param name="movie" value="../image/xindian.swf" />
        <param name="quality" value="high" />
        <embed src="../image/xindian.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="1600" height="92"></embed>
      </object>
      </div>
    </div>
    <div class="denglukuang" align="center">
       <form id="login_form" name="login_form" method="post" action="" >
        <input class="yonghuming" type="text"  name="user" style="font-size:25px;" 
            value="<?php if(isset($_SESSION['user'])) echo $_SESSION['user']?>" />
        <input class="mima" name="pwd" type="password" />
        <div class="btn">
            <input class="dl" name="login" type="button" onclick="javascript:mySubmit()"/>
            <input class="cz" name="reset" type="button" onclick="javascript:myReset();"/>
        </div>
        </form>
       <?PHP if ($errorMsg != '') echo '<span class="span"><strong>' . $errorMsg . '</strong></span>'; ?>
    </div>
    <div class="line"></div>
    <div class="banquan">
    <span class="img"></span>烟台羿中医疗科技有限公司&nbsp;&nbsp;&nbsp;&nbsp;地址：烟台开发区珠江路28号科技大厦617、619&nbsp;&nbsp;&nbsp;&nbsp;电话：0535-6395321
    </div>
</div>
<script type="text/javascript">
function mySubmit(){
    document.getElementById("login_form").submit()
}
function myReset(){
    document.getElementById("login_form").reset();
}
</script>
</body>
</html>
