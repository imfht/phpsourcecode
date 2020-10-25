<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html lang='zh-cn'>
<head>
    <meta charset="utf-8" />
    <title>后台管理系统模板</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/test/Public/static/assets/css/dpl-min.css" rel="stylesheet" type="text/css" />
    <link href="/test/Public/static/assets/css/bui-min.css" rel="stylesheet" type="text/css" />
    <link href="/test/Public/static/assets/css/main-min.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="header">
    <div class="dl-title">
        <span class="lp-title-text">后台管理系统</span>
    </div>
    <div class="dl-log">欢迎您，<span class="dl-log-user"><?php echo session('user_auth.username');?></span> 管理员 <a href="<?php echo U('Public/logout');?>" title="退出系统" class="dl-log-quit">[退出]</a><a href="http://www.builive.com/" target="_blank" title="文档库" class="dl-log-quit">文档库</a>
    </div>
</div>
<div class="content">
    <div class="dl-main-nav">
        <div class="dl-inform"><div class="dl-inform-title">贴心小秘书<s class="dl-inform-icon dl-up"></s></div></div>
        <ul id="J_Nav"  class="nav-list ks-clear">
            <?php if(is_array($topmenu)): $i = 0; $__LIST__ = $topmenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="nav-item <?php if($key == 0): ?>dl-selected<?php endif; ?>"><div class="nav-item-inner nav-<?php echo ($vo["icon"]); ?>"><?php echo ($vo["text"]); ?></div></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
    <ul id="J_NavContent" class="dl-tab-conten">

    </ul>
</div>
<script type="text/javascript" src="/test/Public/static/assets/js/jquery-1.8.1.min.js"></script>
<script type="text/javascript" src="/test/Public/static/assets/js/bui.js"></script>
<script type="text/javascript" src="/test/Public/static/assets/js/config.js"></script>
<script type="text/javascript">
    (function () {
        var ThinkPHP = window.Think = {
            "ROOT": "/test", //当前网站地址
            "APP": "/test/index.php", //当前项目地址
            "PUBLIC": "/test/Public", //项目公共目录地址
            "DEEP": "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL": ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR": ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
</script>
<script>
    BUI.use('common/main',function() {
        var config = <?php echo ($menu_json); ?>;
        new PageUtil.MainPage({
            modulesConfig : config
        });
    });
</script>
</body>
</html>