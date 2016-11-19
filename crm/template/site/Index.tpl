{include file = "header.tpl"}
<form class="form-signin" method="post">
    <lable><font color="red">{$error}</font></lable>
    <input type="text" name="user" class="form-control" style="margin-bottom:10px;" placeholder="请输入用户名" required autofocus>
    <input type="password" name="password" class="form-control" style="margin-bottom:10px;" placeholder="请输入密码" required>
    <button class="btn btn-lg btn-primary btn-block" name="login" type="submit">登录</button>
</form>
{include file = "footer.tpl"}