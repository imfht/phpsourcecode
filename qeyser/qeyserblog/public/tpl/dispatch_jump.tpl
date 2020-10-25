{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Message</title>
    <style type="text/css">
        @font-face {
        font-family:'UKIJ Ekran'; src:url('/UKIJEkran.eot'); src:local('☺'), local("UKIJ Ekran"), url('/UKIJEkran.eot?#iefix') format('embedded-opentype'),  url('/UKIJEkran.woff') format('woff'), url('/UKIJEkran.ttf')  format('truetype');  font-weight: normal;  font-style: normal;}
        *{ padding: 0; margin: 0; }
        body{ background: #fff; font-family: "UKIJ Ekran","Microsoft Yahei","Helvetica Neue",Helvetica,Arial,sans-serif; color: #333; font-size: 16px; direction: rtl;}
        .system-message{ padding: 24px 48px; text-align: center;}
        .system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
        .system-message .jump{ padding-top: 10px; }
        .system-message .jump a{ color: #333; }
        .system-message .success,.system-message .error{ line-height: 1.8em; font-size: 36px; }
        .system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display: none; }
    </style>
</head>
<body>
    <div class="system-message">
        <?php switch ($code) {?>
            <?php case 1:?>
            <h1>:)</h1>
            <p class="success"><?php echo(strip_tags($msg));?></p>
            <?php break;?>
            <?php case 0:?>
            <h1>:(</h1>
            <p class="error"><?php echo(strip_tags($msg));?></p>
            <?php break;?>
        <?php } ?>
        <p class="detail"></p>
        <p class="jump">
             بۇ بەت ئاپتۇماتىك <a id="href" href="<?php echo($url);?>">يۆتكىلىدۇ</a> ساقلاش ۋاقتىڭىز: <b id="wait"><?php echo($wait);?></b>
        </p>
    </div>
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
</body>
</html>