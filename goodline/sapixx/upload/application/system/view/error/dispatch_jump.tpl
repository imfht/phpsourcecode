{__NOLAYOUT__}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="applicable-device" content="mobile">
    <title>友情提示</title>
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        html,body{background: #e9ecf3;overflow: inherit;margin:10px;font-family: "Segoe UI","Lucida Grande",Helvetica,Arial,"Microsoft YaHei",FreeSans,Arimo,FontAwesome,sans-serif;}
        .system-message{padding:22px;background: #FFF}
        .system-message .jump{color: #9a9a9a;font-size: 14px;margin:10px 0px;margin-left:35px}
        .system-message .jump a{ color: #9a9a9a; }
        .system-message .message{line-height: 1.8em;font-size:18px;color: #eb7350;}
        svg{vertical-align: bottom;}
    </style>
</head>
<body>
    <div class="system-message">
        <p class="message">
        <svg t="1566013326523" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="5858" width="30" height="30"><path d="M512.3 746.1c-10.8 0-19.5-8.7-19.5-19.5v-58.4c0-10.8 8.7-19.5 19.5-19.5s19.5 8.7 19.5 19.5v58.4c0 10.8-8.7 19.5-19.5 19.5zM512.3 590.2c-10.8 0-19.5-8.7-19.5-19.5V239.6c0-10.8 8.7-19.5 19.5-19.5s19.5 8.7 19.5 19.5v331.2c0 10.7-8.7 19.4-19.5 19.4z" fill="#FF7878" p-id="5859"></path><path d="M512.3 103.2c225.6 0 409.1 183.5 409.1 409.1 0 225.6-183.5 409.1-409.1 409.1S103.2 737.9 103.2 512.3c0-225.6 183.5-409.1 409.1-409.1m0-39c-247.5 0-448.1 200.6-448.1 448.1 0 247.5 200.6 448.1 448.1 448.1 247.5 0 448.1-200.6 448.1-448.1 0-247.5-200.7-448.1-448.1-448.1z" fill="#FF7878" p-id="5860"></path></svg>
        <?php echo(strip_tags($msg));?> </p>
        <p class="jump">
            您可以点击 <a id="href" href="<?php echo($url);?>"> <?php $data = $data ?: '返回上一页'; echo $data?></a>
        </p>
    </div>
</body>
</html>
