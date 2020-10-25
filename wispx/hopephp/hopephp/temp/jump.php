<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <title>跳转提示</title>
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        /*.vertical-container{
            display: -webkit-flex;
            display: flex;
            -webkit-align-items: center;
            align-items: center;
            -webkit-justify-content: center;
            justify-content: center;
        }*/
    </style>
</head>
<body>
<script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.bootcss.com/layer/3.1.0/layer.js"></script>
<script type="text/javascript">
    (function(){
        layer.alert('<?php echo(strip_tags($result['msg']));?>', {
            title: '提示(<small id="wait"><?php echo($wait);?></small>)：',
            icon: <?php echo $result['code'] ? 1 : 2; ?>,
            yes: function (index) {
                layer.close(index);
                return location.href = '<?php echo($result['url']);?>';
            }
        });
        var wait = document.getElementById('wait');
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = '<?php echo($result['url']);?>';
                clearInterval(interval);
                // 刷新父窗口
                parent.location.reload();
                // 关闭layer Iframe窗口
                var index = parent.layer.getFrameIndex(window.name);
                if(index) {
                    parent.layer.close(index);
                }
            };
        }, 1000);
    })();
</script>
</body>
</html>