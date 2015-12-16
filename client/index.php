<?php
require '../common.php';
include_head('远程动态实时心电监测系统');
session_start();
if (!isset($_SESSION['height'])) {
    $_SESSION['height'] = $_GET['height'];
}
if (isset($_SESSION["isLogin"]) && isset($_SESSION["loginType"]) 
        && $_SESSION["isLogin"] && $_SESSION["loginType"] == 2) {
    user_goto(null, GOTO_FLAG_URL, 'guardian_list.php');
}

$errorMsg = '';
if(isset($_POST['login']) && $_POST['login']) {
    if(trim($_POST["user"]) == ""){
        $errorMsg = '请输入登录名。';
    } else {
        $_SESSION['user'] = $_POST['user'];
    }
    
    if ($errorMsg == '' && trim($_POST["pwd"]) == ""){
        $errorMsg = '请输入密码。';
    }
    
    if ($errorMsg == '') {
        $user = $_POST['user'];
        $pwd = md5($_POST['pwd']);
        
        $ret = Dbi::getDbi()->getAcount($user);
        if (VALUE_DB_ERROR === $ret) {
            user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
        }
        if (empty($ret)) {
            $errorMsg = '您输入的用户不存在。';
        } elseif ($ret['password'] != $pwd) {
            $errorMsg = '您输入的密码有误。';
        } else {
            $accountId = $ret['account_id'];
            $loginType = $ret['type'];
            $hospitalId = $ret['hospital_id'];
            
            setcookie(session_name(), session_id(), time() + 8 * 3600, "/");
            
            $_SESSION['isLogin'] = true;
            $_SESSION['loginType'] = $loginType;
            $_SESSION['hospital'] = $hospitalId;
            $_SESSION["loginId"] = $accountId;
            unset($_SESSION['user']);
            
            user_goto(null, GOTO_FLAG_URL, 'guardian_list.php');
        }
    }
}
?>
<link href="../style/publics.css" rel="stylesheet" type="text/css" />
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
<body>
<div class="bg">
    <div class="logo" align="center"></div>
    <div class="denglukuang" align="center">
      <form id="form1" name="form1" method="post" action="">
      <input class="yonghuming" type="text"  name="user" value="<?php if(isset($_SESSION['user'])) echo $_SESSION['user']?>" />
      <input class="mima" name="pwd" type="password" />
      <div class="btn">
        <input type="submit" name="login" value="登录" style="width: 85px; height: 25px;" />
        <input type="reset" name="reset" value="重置" style="width: 85px; height: 25px;" />
      </div>
      </form>
      <?PHP if ($errorMsg != '') echo '<span class="span"><strong>' . $errorMsg . '</strong></span>'; ?>
    </div>
</div>
</body>
</html>