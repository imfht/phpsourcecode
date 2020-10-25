<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:82:"/www/web/blog_demo_lzcms_top/public_html/application/admin/view/index/home.tpl.php";i:1502978539;s:84:"/www/web/blog_demo_lzcms_top/public_html/application/admin/view/public/toper.tpl.php";i:1502978539;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <title>LzCMS-后台管理中心</title>
    <link rel="stylesheet" href="/static/layui/css/layui.css">
    <link rel="stylesheet" href="/static/css/global.css">
    <script type="text/javascript" src="/static/layui/layui.js"></script>
</head>
<body>
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <div class="main-tab-item">首页面板</div>
    </ul>
    <div class="layui-tab-content index_panel_container">
        <div class="left">
            <blockquote class="layui-elem-quote">
                <p>系统版本：LzCMS <?php echo LZ_VERSION; ?></p><hr>
                <p>ThinkPHP 版本：<?php echo THINK_VERSION; ?></p><hr>
                <p>服务器系统：<?php echo php_uname('s');  ?></p><hr>
                <p>WEB运行环境：<?php echo function_exists('apache_get_version')?apache_get_version():$_SERVER["SERVER_SOFTWARE"];  ?></p><hr>
                <p>运行PHP版本：<?php echo 'PHP'.phpversion();?></p><hr>
                <p>数据库信息：MySQL&nbsp;<?php echo @mysql_get_server_info();?></p><hr>
                <p>上传大小限制：<?php echo ini_get('upload_max_filesize');?></p><hr>
                <p>POST大小限制：<?php echo ini_get('post_max_size');?></p>
            </blockquote> 
            <blockquote class="layui-elem-quote">
                <p>系统名称：LzCMS（老张博客系统）</p><hr>
                <p>开发作者：老张</p><hr>
                <p>软件官网： <a target="_blank" href="http://www.phplaozhang.com">http://www.phplaozhang.com</a></p><hr>
                <p>项目地址： <a target="_blank" href="https://git.oschina.net/phplaozhang/LzCMS-LaoZhangBoKeXiTong">https://git.oschina.net/phplaozhang/LzCMS-LaoZhangBoKeXiTong</a></p><hr>
                <p>QQ交流群：602099721&nbsp;&nbsp;<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=9e3d8ac1ba7022b4cc6a492c7645e0198a06afbde7e6d55cab5ca5dbbac5c186"><img border="0" src="//pub.idqqimg.com/wpa/images/group.png" alt="老张博客-Lz-CMS交流群" title="老张博客-Lz-CMS交流群"></a></p><hr>
                <p>BUG反馈： <a target="_blank" href="http://www.phplaozhang.com/feedback.html">http://www.phplaozhang.com/feedback.html</a></p>
            </blockquote>
        </div>
        <div class="right">
           <blockquote class="layui-elem-quote update_log"> 
                <p style="font-weight: bold;">v1.1.3更新日志2017-8-7</p>
                <p>
                    1、单页模型页面描述内容调整<br>  
                    2、搜狐畅言兼容http和https<br>
                    3、IIS伪静态隐藏index.php配置<br>  
                    4、修复QQ登陆无效问题<br>  
                </p>
                <hr>
                <p style="font-weight: bold;">v1.1.2更新日志2017-7-26</p>
                <p>
                    1、新增置顶功能 <br>  
                    2、图集多图上传支持多选上传<br>  
                    3、文章支持excel导入和导出excel<br>  
                    4、修复已知bug<br>  
                </p>
                <hr>
                <p style="font-weight: bold;">v1.1.1更新日志2017-7-18</p>
                <p>
                    1、修复数据库字段必填带来的错误 <br>  
                    2、修复apache伪静态配置文件错误导致的 No input file specified
                </p>
                <hr>
                <p style="font-weight: bold;">v1.1.0更新日志2017-6-26</p>
                <p>
                    1、增加源码一键安装<br>  
                    2、增强图集模型详情页轮播<br>  
                    3、列表页图片懒加载<br>  
                    4、搜索关键词标红<br>  
                    5、增强前台内容可控性如：首页banner、首页网站理念等可在后台修改<br>  
                    6、部分功能优化<br>  
                    7、部分bug修正
                </p>
            </blockquote> 
        </div>
        

    </div>
</div>
</body>
</html>