<?php
/**
 * 变量：
 * --$siteName：站点名字
 * --$description:描述
 * --$siteLogo：站点LOGO
 * --$siteUrl：站点地址
 */
?>
<!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{'维护中-'.$siteName}}</title>
        <meta name="description" content="{{$description}}">

        <!-- Bootstrap -->
        <link href="/themes/default/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="/themes/default/css/main.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            .maintenance{
                margin-top: 200px;
            }
        </style>
    </head>
    <body>
        <div class="container maintenance">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">{{$siteName}}</h3>
                            </div>
                            <div class="panel-body">
                                站点正在维护中...
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </div>
        </div>
        <script src="/themes/default/bootstrap/js/jquery.min.js"></script>
        <script src="/themes/default/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>
{{exit}}