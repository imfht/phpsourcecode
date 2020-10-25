<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>错误提示</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #666;
                display: table;
                font-weight: 100;
                font-family: font-family: 'Microsoft YaHei', '微软雅黑';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 24px;
                margin-bottom: 40px;
            }

            a {
                text-decoration: none;
                color: #999;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">您所查找的页面无法找到，请重试</div>
                <div class="title"><a href="{{ url('/') }}">返回首页</a></div>
            </div>
        </div>
    </body>
</html>
