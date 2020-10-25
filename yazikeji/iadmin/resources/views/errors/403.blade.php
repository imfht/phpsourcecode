<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>错误信息</title>
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
<h1>403</h1>
<h3>ERROR: {{$exception->getMessage()}}</h3>
<hr>
<p>详细错误请看上方说明， <a href="javascript:history.back();">点击这里</a> 返回上一级.</p>


<div id="tqShowIP" class="tqShowIP_right">116.211.167.14</div></body></html>