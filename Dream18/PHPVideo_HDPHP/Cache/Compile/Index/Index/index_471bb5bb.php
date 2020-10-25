<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $hd['config']['WEBNAME'];?></title>
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="http://localhost/PHPUnion/Static/jquery-1.11.1.min.js"></script>
    <link type="text/css" rel="stylesheet" href="http://localhost/PHPUnion/Static/hdjs/hdjs.css"/>
    <script type="text/javascript" src="http://localhost/PHPUnion/Static/hdjs/hdjs.min.js"></script>
    <link type="text/css" rel="stylesheet" href="Theme/Default/css/common.css"/>
    <link type="text/css" rel="stylesheet" href="Theme/Default/css/index.css"/>
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="http://localhost/PHPUnion/Static/bootstrap/css/bootstrap.min.css">

    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="http://localhost/PHPUnion/Static/bootstrap/css/bootstrap-theme.min.css">
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <link type="text/css" rel="stylesheet" href="Theme/Default/css/ie.css"/>
    <![endif]-->
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="http://localhost/PHPUnion/Static/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>

<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<div id="header">
    <div class="header-warp">
        <div id="header-right">
                <?php if(IS_LOGIN){ ?>
                <div class="dropdown">
                    <div id="dropdownMenu1" data-toggle="dropdown">
                        <img src="<?php echo $hd['session']['user']['icon'];?>" class="user" /> <?php echo $hd['session']['user']['username'];?>
                        <span class="caret"></span>
                    </div>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://localhost/PHPUnion/index.php?m=Member&c=Content&a=content&mid=1">文章管理</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://localhost/PHPUnion/index.php?m=Member&c=Account&a=personal">个人资料</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://localhost/PHPUnion/index.php?m=Member&c=Login&a=out">退出</a></li>
                    </ul>
                </div>
            <?php }else{ ?>
                <a href="<?php echo U('Member/Login/login');?>" class="bt-primary">登录</a>
                <a href="<?php echo U('Member/Login/reg');?>" class="bt-default">注册</a>
            <?php } ?>
        </div>
        <a id="logo" href="http://localhost/PHPUnion/index.php" title="后盾网开源"></a>
        <ul id="header-nav">
            <li     <?php if(!isset($_GET['cid'])){ ?>class="nav-current"<?php } ?>>
                <a href="http://localhost/PHPUnion/index.php">首页</a>
            </li>
            <channel type="top">
                <li     <?php if($_GET['cid']==$field['cid'] || Data::isChild(S(category),$_GET['cid'],$field['cid'])){ ?>class="nav-current"<?php } ?>>
                <?php echo $field['catlink'];?>
                </li>
            </channel>
        </ul>
    </div>
</div>
<div id="photo">
    <div class="container">
        <div class="row">
            <div class="col-md-5 main-cta-left">
                <h3 class="title">免费、简单的开源产品</h3>
                <h4 class="info">专注提高网站开发效率</h4>
                <a href="http://localhost/PHPUnion/index.php?m=Index&c=Category&a=index&mid=1&cid=6" class="btn btn-large btn-success cta-button">立即下载</a>
            </div>
            <div class="col-md-7">
                <div id="slide">
                    <a href="http://localhost/PHPUnion/index.php?m=Index&c=Category&a=index&mid=1&cid=6" title=" HDCMS快速建站利器"><img src="Theme/Default/images/screenshot2.png"/></a>
                    <a href="http://localhost/PHPUnion/index.php?m=Index&c=Category&a=index&mid=1&cid=6" title=" HDPHP百分百免费、性能强劲的开源框架"><img src="Theme/Default/images/screenshot1.png"/></a>
                </div>
                <script>
                    $(function () {
                        $('#slide').slide({
                            width: 560,//宽度
                            height: 397,//高度
                            timeout: 3,//间隔时间
                            bgcolor: '#1B2527',//背景颜色
                            bgopacity: 0.5,//背景透明度
                            textColor: '#fff'//文字颜色
                        });
                    })
                </script>
            </div>
        </div>
    </div>
</div>
<div id="features" class="container">
    <h3 class="text-center">为什么选择后盾网开源产品？</h3>

    <div class="row">
        <div class="col-md-4">
            <img style="top: 0px; opacity: 1;" src="Theme/Default/images/site-tour-why-create@2x.png"
                 alt="create with connect" class="img-circle img-responsive why-section">
            <h4 class="text-center">功能丰富</h4>

            <p class="text-center"> 提供丰富的内置功能与扩展机置，可以根据需要随意定制</p>
        </div>
        <div class="col-md-4">
            <img style="top: 0px; opacity: 1;" src="Theme/Default/images/site-tour-why-create@2x.png"
                 alt="create with connect" class="img-circle img-responsive why-section">
            <h4 class="text-center">快速上手</h4>

            <p class="text-center"> 每一个开源产品我们都提供完善的使用视频教程与交流渠道</p>
        </div>
        <div class="col-md-4">
            <img style="top: 0px; opacity: 1;" src="Theme/Default/images/site-tour-why-create@2x.png"
                 alt="create with connect" class="img-circle img-responsive why-section">
            <h4 class="text-center">安全可靠</h4>

            <p class="text-center"> 所有产品均经过严格测试，可以放心用在网站开发中</p>
        </div>
    </div>
</div>
<div class="container" id="article">
    <div class="row">
        <div class="col-md-6">
            <div class="news">
                <h4><a href="http://localhost/PHPUnion/index.php?m=Index&c=Category&a=index&mid=1&cid=1">更多>></a>框架资讯</h4>
                <ul>
                    <arclist row="6" cid="1" sub_channel="1">
                        <li>
                            <a href="<?php echo $field['url'];?>"><span class="datetime"><?php echo $field['time'];?></span><?php echo $field['title'];?></a>
                        </li>
                    </arclist>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="news">
                <h4><a href="http://localhost/PHPUnion/index.php?m=Index&c=Category&a=index&mid=1&cid=2">更多>></a>CMS资讯</h4>
                <ul>
                    <arclist row="6" cid="2" sub_channel="1">
                        <li>
                            <a href="<?php echo $field['url'];?>"><span class="datetime"><?php echo $field['time'];?></span><?php echo $field['title'];?></a>
                        </li>
                    </arclist>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<div id="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <a href="http://www.houdunwang.com" target="_blank">高端PHP培训</a>|
                <a href="http://www.kuaixuewang.com" target="_blank">快学网</a>|
                <a href="http://www.kuaipinwang.com" target="_blank">快聘网</a>|
                <a href="http://localhost/PHPUnion/index.php?g=Addon&m=Sitemap&c=Index&a=index">网站地图</a>
                <br>
                © 2012 - 2014 hdphp.com. All Rights Reserved (京ICP备12048441号-1)
                <script type="text/javascript" src="http://tajs.qq.com/stats?sId=39551033" charset="UTF-8"></script>
                <!--站长统计Start-->
                <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1253902030'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/z_stat.php%3Fid%3D1253902030' type='text/javascript'%3E%3C/script%3E"));</script>
                <!--站长统计End-->
            </div>
        </div>
    </div>
</div>
</body>
</html>