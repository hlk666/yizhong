{include file = "header.tpl"}
<div class="row">
  <div class="col-sm-12">
    <div class="alert alert-danger" role="alert">{$message}</div>
    <div style="font-size:20px;"><span id="goto" style="color:red;">5</span>秒后自动跳转。</div>
    <script type="text/javascript">
    function countDown(seconds, url)
    {
        var element = document.getElementById('goto');
        element.innerHTML = seconds;
        if (--seconds > 0) {
            setTimeout("countDown(" + seconds + ", '" + url + "')", 1000);
        }
        else{
            if (url == 'back') {
                history.back();
            } else {
                location.href = url;
            }
        }
    }
    countDown(5, 'back');
    </script>
  </div>
</div> <!-- /row -->
{include file = "footer.tpl"}