<html><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>羿中医疗科技有限公司管理系统</title>
    
    <link href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="a.css">
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
<label>设置查询时间范围</label>
<label>开始时间</label><input type="text" name="start_time" onclick="SelectDate(this,'yyyy-MM-dd')" />
<label>结束时间</label><input type="text" name="start_time" onclick="SelectDate(this,'yyyy-MM-dd')" />
<button type="submit" class="btn btn-sm btn-info" name="query">查找</button>
</div>

<div class="col-sm-3 blog-main">
<table class="table table-bordered" style="margin:10 0 0 10;">
<thead><tr><th>ID</th><th>医院</th><th>数量</th></tr></thead>
<tbody>
<tr><td>1</td><td>山东大学附属千佛山医院</td><td>3</td></tr>
<tr><td>2</td><td>新泰市人民医院</td><td>3</td></tr>
<tr><td>3</td><td>新汶矿务局中心医院心内一科</td><td>3</td></tr>
<tr><td>4</td><td>新汶矿务局中心医院心内二科</td><td>3</td></tr>
<tr><td>5</td><td>天宝中心卫生院</td><td>3</td></tr>
<tr><td>6</td><td>谷里镇中心医院</td><td>3</td></tr>
<tr><td>7</td><td>放城中心卫生院</td><td>3</td></tr>
<tr><td>8</td><td>淄博市中医医院</td><td>3</td></tr>
<tr><td>9</td><td>淄矿集团中心医院</td><td>3</td></tr>
<tr><td>10</td><td>招远市中医医院</td><td>3</td></tr>
<tr><td>11</td><td>淄川区峨庄卫生院</td><td>3</td></tr>
<tr><td>12</td><td>周村区王村镇中心卫生院</td><td>3</td></tr>
<tr><td>13</td><td>淄川区淄河卫生院</td><td>3</td></tr>
<tr><td>14</td><td>周村区南郊镇中心卫生院</td><td>3</td></tr>
<tr><td>15</td><td>淄博淄川区仁和医院</td><td>3</td></tr>
<tr><td>16</td><td>淄川区领子卫生院</td><td>3</td></tr>
<tr><td>17</td><td>淄博光正公司医院</td><td>3</td></tr>
<tr><td>18</td><td>淄川区太河卫生院</td><td>3</td></tr>
<tr><td>19</td><td>淄川区黑旺卫生院</td><td>3</td></tr>
</tbody>
</table>
</div>

<div class="col-sm-9 blog-sidebar">
  <table class="table table table-bordered" style="margin-top:10;">
    <thead>
      <tr>
        <th>序号</th>
        <th>医院</th>
        <th>姓名</th>
        <th>开始时间</th>
        <th>结束时间</th>
        <th>设备号</th>
        <th>开单医生</th>
        <th>监护状态</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>1</td><td>羿中医疗测试医院</td><td>张三</td><td>2011-01-01 00:00:00</td><td>2011-01-01 00:00:00</td><td>80200011</td><td>王医生</td><td>正在监护</td></tr>
    </tbody>
  </table>
<div style="text-align:left;">
<ul class="pagination">
<li><a href="?page=1">首页</a></li><li class="disabled"><a href="?page=1">前页</a></li>
<li class="active"><a href="?page=1">1</a></li><li><a href="?page=10">10</a></li>
<li><a href="?page=2">后页</a></li><li><a href="?page=10">尾页</a></li></ul>
</div>
<script type="text/javascript" src="yizhong.js"></script>
<script src="http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js"></script>
<script src="http://apps.bdimg.com/libs/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="adddate.js"></script>
<script type="text/javascript">
    $('.selectpicker').selectpicker();
</script>
</body></html>