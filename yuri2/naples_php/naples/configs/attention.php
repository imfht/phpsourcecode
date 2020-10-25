<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/1
 * Time: 10:14
 */

//在此定义各个提示信息模板
return [
    'success'=> <<<ETO
    <html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>提示</title>
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        body{ background: #fff; font-family: "Microsoft Yahei","Helvetica Neue",Helvetica,Arial,sans-serif; color: #333; font-size: 16px; }
        .system-message{ padding: 24px 48px; }
        .system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
        .system-message .jump{ padding-top: 10px; }
        .system-message .jump a{ color: #333; }
        .system-message .success,.system-message .error{ line-height: 1.8em; font-size: 36px; }
        .system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display: none; }
    </style>
</head>
<body>
    <div class="system-message">
                    <h1>:)</h1>
            <p class="success">[$1=Success!]</p>
                    <p class="detail"></p>
        <p id='jump' class="jump">
            页面自动 <a id="href" href="[$2=]" url="[$2=]">跳转</a> 等待时间： <b id="wait">[$3=3]</b>
        </p>
    </div>
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
                url = '[$2=]';
            if(url==''){
                document.getElementById('jump').style.display='none';
            }else{
                var interval = setInterval(function(){
                    var time = --wait.innerHTML;
                    if(time <= 0) {
                        if(url=='back'){
                            location.href = 'javascript:history.go(-1);';
                        }else{
                            location.href = href;
                        }
                        
                        clearInterval(interval);
                    };
                }, 1000);
            }
            
        })();
    </script>
</body>
</html>

ETO
    ,
    'error'=> <<<ETO
    <html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>提示</title>
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        body{ background: #fff; font-family: "Microsoft Yahei","Helvetica Neue",Helvetica,Arial,sans-serif; color: #333; font-size: 16px; }
        .system-message{ padding: 24px 48px; }
        .system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
        .system-message .jump{ padding-top: 10px; }
        .system-message .jump a{ color: #333; }
        .system-message .success,.system-message .error{ line-height: 1.8em; font-size: 36px; }
        .system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display: none; }
    </style>
</head>
<body>
    <div class="system-message">
                    <h1>:(</h1>
            <p class="success">[$1=Error...]</p>
                    <p class="detail"></p>
        <p id='jump' class="jump">
            页面自动 <a id="href" href="[$2=]" url="[$2=]">跳转</a> 等待时间： <b id="wait">3</b>
        </p>
    </div>
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
                url = '[$2=]';
            if(url==''){
                document.getElementById('jump').style.display='none';
            }else{
                var interval = setInterval(function(){
                    var time = --wait.innerHTML;
                    if(time <= 0) {
                        if(url=='back'){
                            location.href = 'javascript:history.go(-1);';
                        }else{
                            location.href = href;
                        }
                        
                        clearInterval(interval);
                    };
                }, 1000);
            }
            
        })();
    </script>
</body>
</html>

ETO
    ,
];?>

