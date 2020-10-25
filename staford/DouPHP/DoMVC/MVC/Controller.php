<?php

/**
 * 控制器基础类文件
 * @abstract 提供基础的控制器逻辑处理支持
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Controller extends Template
{
    /**
     * @name success 成功提示
     * @param string $msg 提示信息
     * @param string $url 跳转地址
     * @param integer $time 等待时长
     */
    protected function success($msg = '操作成功', $url = '', $time = 5)
    {
        echo <<< HTML
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>操作成功</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; max-width: 500px; margin: 0 auto; text-align: center; }
.system-message{ padding: 24px 48px; }
.system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
.system-message .jump{ padding-top: 10px}
.system-message .jump a{ color: #333;}
.system-message .success,.system-message .error{ line-height: 1.8em; font-size: 36px }
.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
</style>
</head>
<body>
<div class="system-message">
<img src="data:image/jpg;base64,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" alt="开心~~" title="啊哈哈~~成功咯~~开心ing~~" />
<p class="success">{$msg}</p>
<p class="detail"></p>
<p class="jump">
页面自动<a id="href" href="{$url}">跳转</a>中，等待时间： <b id="wait">{$time}</b>
</p>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
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
HTML;
        exit;
    }

    /**
     * @name error 失败提示
     * @param string $msg 提示信息
     * @param string $url 跳转地址
     * @param integer $time 等待时间
     */
    protected function error($msg = '操作失败', $url = '', $time = 5)
    {
        echo <<< HTML
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>操作失败</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; max-width: 500px; margin: 0 auto; text-align: center; }
.system-message{ padding: 24px 48px; }
.system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
.system-message .jump{ padding-top: 10px}
.system-message .jump a{ color: #333;}
.system-message .success,.system-message .error{ line-height: 1.8em; font-size: 36px }
.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
</style>
</head>
<body>
<div class="system-message">
<img src="data:image/jpg;base64,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" alt="伤心~~" title="噢~出错啦~~都是我的错~~求你原谅我~~~" />
<p class="error">{$msg}</p>
<p class="detail"></p>
<p class="jump">
页面自动<a id="href" href="{$url}">跳转</a>中，等待时间： <b id="wait">{$time}</b>
</p>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
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
HTML;
        exit;
    }

    /**
     * @name urlCreate
     * @abstract URL创建器
     * @param string $controller 控制器
     * @param string $action 操作
     * @param array  $params 参数
     */
    protected function urlCreate($controller, $action, $params = array())
    {
        $param = '';
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $param .= '&' . $key . '=' . $val;
            }
        }
        return '/index.php?c=' . $controller . '&a=' . $action . $param;
    }
}

?>