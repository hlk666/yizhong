<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>{$title}</title>
    <meta name="Keywords" content="{$keywords}" />
    <meta name="Description" content="{$description}" />
    
    <link href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap-theme.min.css"> -->
    <link rel="stylesheet" href="{$domain}/css/common.css">
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container" style="background-color:#FFF">
    <div class="row">
      {if $isLogin}
          <div class="col-sm-2 blog-main" style="font-size:18px;">
            <ul class="nav nav-sidebar">
              <li><a href="{$domain}user">业务员列表</a></li>
              <li><a href="{$domain}add_user">添加业务员</a></li>
            </ul>
            <ul class="nav nav-sidebar">
              <li><a href="{$domain}schedule">客户推进记录</a></li>
              <li><a href="{$domain}add_schedule">添加记录</a></li>
            </ul>
            <ul class="nav nav-sidebar">
              <li><a href="{$domain}hospital">医院列表</a></li>
              <li><a href="{$domain}add_hospital">添加医院</a></li>
            </ul>
            <ul class="nav nav-sidebar">
              <li><a href="{$domain}agency">代理商列表</a></li>
              <li><a href="{$domain}add_agency">添加代理商</a></li>
            </ul>
          </div>
        <div class="col-sm-10 blog-sidebar">
          <div style="margin-top:10px;margin-bottom:10px;font-size:x-large;text-align:center;"><h2>{$subTitle}</h2></div>
        {else}
          <div class="col-sm-12 blog-sidebar">
            <div style="margin-top:10px;margin-bottom:10px;text-align:center;"><h2>{$subTitle}</h2></div>
        {/if}