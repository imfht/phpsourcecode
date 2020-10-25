<?php
    if(!function_exists('parse_padding')){
        function parse_padding($source){
            $length  = strlen(strval(count($source['source']) + $source['first']));
            return 40 + ($length - 1) * 8;
        }
    }
    if(!function_exists('parse_class')){
        function parse_class($name){
            $names = explode('\\', $name);
            return '<abbr title="'.$name.'">'.end($names).'</abbr>';
        }
    }
    if(!function_exists('parse_file')){
        function parse_file($file, $line){
            return '<a class="toggle" title="'."{$file} line {$line}".'">'.basename($file)." line {$line}".'</a>';
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>系统发生错误</title>
    <meta name="robots" content="noindex,nofollow" />
    <style>
    body{color:#333;font:16px Verdana,"Helvetica Neue",helvetica,Arial,'Microsoft YaHei',sans-serif;margin:0;padding:0 20px 20px}
    .info{padding:10px;background: #FFF;}
    h1{font-size:18px;font-weight:500;line-height:32px}
    h2{color:#4288ce;font-weight:400;padding:6px 0;margin:6px 0 0;font-size:18px;border-bottom:1px solid #eee}
    h3{margin:12px;font-size:16px;font-weight:bold}
    abbr{cursor:help;text-decoration:underline;text-decoration-style:dotted}
    a{color:#868686;cursor:pointer}
    a:hover{text-decoration:underline}.line-error{background:#f8cbcb}
    .echo table{width:100%}
    .echo pre{padding:16px;overflow:auto;font-size:85%;line-height:1.45;background-color:#f7f7f7;border:0;border-radius:3px;font-family:Consolas,"Liberation Mono",Menlo,Courier,monospace}.echo pre>pre{padding:0;margin:0}.exception{margin-top:20px}.exception .message{padding:12px;border:1px solid #ddd;border-bottom:0 none;line-height:18px;font-size:16px;border-top-left-radius:4px;border-top-right-radius:4px;font-family:Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑"}.exception .code{float:left;text-align:center;color:#fff;margin-right:12px;padding:16px;border-radius:4px;background:#999}.exception .source-code{padding:6px;border:1px solid #ddd;background:#f9f9f9;overflow-x:auto}.exception .source-code pre{margin:0}.exception .source-code pre ol{margin:0;color:#4288ce;display:inline-block;min-width:100%;box-sizing:border-box;font-size:14px;font-family:"Century Gothic",Consolas,"Liberation Mono",Courier,Verdana;padding-left:<?php echo(isset($source) &&!empty($source)) ? parse_padding($source):40;?>px}.exception .source-code pre li{border-left:1px solid #ddd;height:18px;line-height:18px}.exception .source-code pre code{color:#333;height:100%;display:inline-block;border-left:1px solid #fff;font-size:14px;font-family:Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑"}.exception .trace{padding:6px;border:1px solid #ddd;border-top:0 none;line-height:16px;font-size:14px;font-family:Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑"}
    .exception .trace ol{margin:12px}
    .exception .trace ol li{padding:2px 4px}
    .exception div:last-child{border-bottom-left-radius:4px;border-bottom-right-radius:4px}
    </style>
</head>
<body>
    <div class="echo"><?php echo $echo;?></div>
    <?php if(\think\facade\App::isDebug()) { ?>
    <div class="exception">
    <div class="message">
        <div class="info">
            <div>
                <h2>[<?php echo $code; ?>]&nbsp;<?php echo sprintf('%s in %s', parse_class($name), parse_file($file, $line)); ?></h2>
            </div>
            <div><h1><?php echo nl2br(htmlentities($message)); ?></h1></div>
        </div>
    </div>
	<?php if(!empty($source)){?>
        <div class="source-code">
            <pre class="prettyprint lang-php"><ol start="<?php echo $source['first']; ?>"><?php foreach ((array) $source['source'] as $key => $value) { ?><li class="line-<?php echo $key + $source['first']; ?>"><code><?php echo htmlentities($value); ?></code></li><?php } ?></ol></pre>
        </div>
	<?php }?>
    </div>
    <?php } else { ?>
    <div class="exception"><div class="info"><h1><?php echo htmlentities($message); ?></h1></div></div>
    <?php } ?>
</body>
</html>
