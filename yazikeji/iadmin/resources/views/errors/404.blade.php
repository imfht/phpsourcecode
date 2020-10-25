<!DOCTYPE html>
<html>
<head>
    <title>404 Not Found</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        body {
            color: #666;
            text-align: center;
            font-family: Helvetica, 'microsoft yahei', Arial, sans-serif;
            margin:0;
            width: 800px;
            margin: auto;
            font-size: 14px;
        }
        h1 {
            font-size: 56px;
            line-height: 100px;
            font-weight: normal;
            color: #456;
        }
        h2 { font-size: 24px; color: #666; line-height: 1.5em; }

        h3 {
            color: #456;
            font-size: 20px;
            font-weight: normal;
            line-height: 28px;
        }

        hr {
            margin: 18px 0;
            border: 0;
            border-top: 1px solid #EEE;
            border-bottom: 1px solid white;
        }

        a{
            color: #17bc9b;
            text-decoration: none;
        }
    </style>
</head>
<body>
<h1>404</h1>
<h3>ERROR: {{$exception->getMessage() ? $exception->getMessage() : '访问资源不存在'}}</h3>
<hr>
<p>访问资源不存在或者您没有权限访问， <a href="javascript:history.back();">点击这里</a> 返回上一级.</p>

</body>
</html>

