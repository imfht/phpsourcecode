{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <title>跳转提示</title>
    {load href="__static__/common/jquery/jquery-2.2.3.min.js" /}
    {load href="__static__/SemanticUI/semantic.min.css" /}
    {load href="__static__/SemanticUI/semantic.min.js" /}

</head>
<body>

    <div class="ui card" style="text-align:center;width:80%;position: fixed;top: 20%;left: 10%">
        <?php if( $code == 1 ) {?>
            <div class="ui green inverted segment" style="margin: 0px;">
                <i class="ui check circle icon massive"></i>
            </div>
        <?php }else{?>
            <div class="ui red inverted segment" style="margin: 0px;">
                <i class="ui remove circle icon massive"></i>
            </div>
        <?php }?>
        <div class="content" style="line-height: 2em">

            <span class="header"><?php echo($msg); ?></span>

            <div class="meta">
                	将在<span id="left"><?php echo($wait); ?></span>S后自动跳转
            </div>
        </div>
        <span style="display: none" id="href"><?php echo($url); ?></span>
        <div class="ui bottom attached indicating progress" id="amanege-bar">
            <div class="bar"></div>
        </div>
    </div>
</body>
<script type="text/javascript">
    (function(){
        var wait = 0,left = $('#left').text();
        var href = $('#href').text();
        var each = 100/left;
        var interval = setInterval(function(){
            wait = wait + each;
            left = left - 1;
            if(wait > 100) {
                location.href = href;
                clearInterval(interval);
                return ;
            }
            $('#left').text(left);
            $('#amanege-bar').progress({
                percent: wait
            });
        }, 1000);
    })();
</script>
</html>
