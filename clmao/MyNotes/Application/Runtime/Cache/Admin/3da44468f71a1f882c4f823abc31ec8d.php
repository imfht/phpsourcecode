<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Clmao">
        <meta http-equiv="Cache-Control" content="no-transform" />
        <meta http-equiv="Cache-Control" content="no-siteapp"/>
        <title><?php echo ($title); ?></title>
        <link rel="apple-touch-icon" href="/Public/appicon.png">
        <link rel="shortcut icon" href="/Public/appicon.png">
        <link href="/Public/zui/zui.min.css" rel="stylesheet">
        <script src="/Public/js/jquery.min.js"></script>
        <script src="/Public/zui/zui.min.js"></script>
        
    </head>
    <?php if(is_mobile() == true): ?><style>
            body{margin-top: 40px;}
        </style>
        <nav  class="navbar navbar-inverse navbar-fixed-top"  role="navigation">
    <div  class="navbar-header">
        <button  type="button"  class="navbar-toggle collapsed"  data-toggle="collapse"  data-target="#navbar">
            <span  class="sr-only">Toggle navigation</span>
            <span  class="icon-bar"></span>
            <span  class="icon-bar"></span>
            <span  class="icon-bar"></span>
        </button>
        <a  class="navbar-brand"  href="<?php echo U('Admin/Admin/help');?>"><?php echo getSiteOption('siteName'); ?> 后台管理</a>
    </div>
    <div  class="navbar-collapse navbar-collapse-example collapse"  id="navbar"  style="height: 1px;">
        <ul  class="nav navbar-nav">
            <li  class="dropdown">
                <a  href="<?php echo U('Admin/Admin/addContent');?>"  class="dropdown-toggle"  data-toggle="dropdown"><span  id="navbarCurrent">文章管理</span> <b  class="caret"></b></a>
                <ul  class="dropdown-menu"  role="menu">
                    <li><a  href="<?php echo U('Admin/Admin/addContent');?>">撰写文章</a></li>
                    <li><a  href="<?php echo U('Admin/Admin/listContent');?>">所有文章</a></li>
                    <li><a  href="<?php echo U('Admin/Admin/addCategory');?>">添加分类</a></li>
                    <li><a  href="<?php echo U('Admin/Admin/listContent');?>">所有分类</a></li>
                    <li><a  href="<?php echo U('Admin/Admin/listDraft');?>">草稿箱</a></li>
                    <li><a  href="<?php echo U('Admin/Admin/listCallback');?>">回收站</a></li>
                </ul>
            </li>
             
             <li  class="dropdown">
                <a  href="<?php echo U('Admin/Media/index');?>"  class="dropdown-toggle"  data-toggle="dropdown"><span  id="navbarCurrent">多媒体管理</span> <b  class="caret"></b></a>
                <ul  class="dropdown-menu"  role="menu">
                    <li><a  href="<?php echo U('Admin/Media/index');?>">媒体库</a></li>
                    <li><a  href="<?php echo U('Admin/Media/add');?>">添加媒体</a></li>
                </ul>
            </li>
          
             <li  class="dropdown">
                <a  href="<?php echo U('Admin/Admin/addLinks');?>"  class="dropdown-toggle"  data-toggle="dropdown"><span  id="navbarCurrent">链接管理</span> <b  class="caret"></b></a>
                <ul  class="dropdown-menu"  role="menu">
                    <li><a  href="<?php echo U('Admin/Links/addLinks');?>">添加链接</a></li>
                    <li><a  href="<?php echo U('Admin/Links/listLinks');?>">查看链接</a></li>
                </ul>
            </li>
           

             <li  class="dropdown">
                <a  href="<?php echo U('Admin/Rbac/index');?>"  class="dropdown-toggle"  data-toggle="dropdown"><span  id="navbarCurrent">权限管理</span> <b  class="caret"></b></a>
                <ul  class="dropdown-menu"  role="menu">
                    <li><a  href="<?php echo U('Admin/Rbac/index');?>">用户列表</a></li>
                    <li><a  href="<?php echo U('Admin/Rbac/role');?>">角色列表</a></li>
                    <li><a  href="<?php echo U('Admin/Rbac/node');?>">节点列表</a></li>
                    <li><a  href="<?php echo U('Admin/Rbac/addUser');?>">所有分类</a></li>
                    <li><a  href="<?php echo U('Admin/Rbac/addRole');?>">添加用户</a></li>
                    <li><a  href="<?php echo U('Admin/Rbac/index');?>">添加角色</a></li>
                </ul>
            </li>
            <?php $conf = 'config.php'; $categoryView = 'category.html'; $contentView = 'content.html'; $footerView = 'footer.html'; $headerView = 'header.html'; $indexView = 'index.html'; $cssView = 'mobile.css'; ?>
             <li  class="dropdown">
                <a  href="<?php echo U('Admin/Admin/addContent');?>"  class="dropdown-toggle"  data-toggle="dropdown"><span  id="navbarCurrent">在线编辑</span> <b  class="caret"></b></a>
                <ul  class="dropdown-menu"  role="menu">
                    <li><a  href="<?php echo U('Admin/Config/config',array('remark'=>'首页模板','filename'=>$indexView)); ?>">首页模板</a></li>
                    <li><a  href="<?php echo U('Admin/Config/config',array('remark'=>'文章模板','filename'=>$contentView)); ?>">文章模板</a></li>
                    <li><a  href="<?php echo U('Admin/Config/config',array('remark'=>'分类模板','filename'=>$categoryView)); ?>">分类模板</a></li>
                    <li><a  href="<?php echo U('Admin/Config/config',array('remark'=>'头部文件','filename'=>$headerView)); ?>">头部文件</a></li>
                    <li><a  href="<?php echo U('Admin/Config/config',array('remark'=>'底部模板','filename'=>$footerView)); ?>">底部文件</a></li>
                    <li><a  href="<?php echo U('Admin/Config/config',array('remark'=>'配置文件','filename'=>$conf)); ?>">配置文件</a></li>
                </ul>
            </li>
  
            <li  class="dropdown">
                <a  href="<?php echo U('Admin/Admin/createHtml');?>"  class="dropdown-toggle"  data-toggle="dropdown"><span  id="navbarCurrent">SiteMap管理</span> <b  class="caret"></b></a>
                <ul  class="dropdown-menu"  role="menu">
                    <li><a  href="<?php echo U('Admin/SiteMap/createHtml',array('act'=>'r'));?>">更新HTML地图</a></li>
                    <li><a  href="<?php echo U('Admin/SiteMap/createXML',array('act'=>'r'));?>">更新XML地图</a></li>
                    <li><a  href="<?php echo $app_path . '/sitemap.html'; ?>">浏览HTML地图</a></li>
                    <li><a  href="<?php echo $app_path . '/sitemap.xml'; ?>">浏览XML地图</a></li>
                </ul>
            </li>
            
             <li  class="dropdown">
                <a  href="<?php echo U('Admin/Data/sql');?>"  class="dropdown-toggle"  data-toggle="dropdown"><span  id="navbarCurrent">数据管理</span> <b  class="caret"></b></a>
                <ul  class="dropdown-menu"  role="menu">
                    <li><a  href="<?php echo U('Admin/Data/sql');?>">SQL命令</a></li>
                    <li><a  href="<?php echo U('Admin/Data/backupDB');?>">数据备份</a></li>
                    <li><a  href="<?php echo U('Admin/Data/opimize');?>">修复优化</a></li>
                </ul>
            </li>
            
            <li  class="dropdown">
                <a  href="<?php echo U('Admin/Char/content');?>"  class="dropdown-toggle"  data-toggle="dropdown"><span  id="navbarCurrent">图表统计</span> <b  class="caret"></b></a>
                <ul  class="dropdown-menu"  role="menu">
                    <li><a  href="<?php echo U('Admin/Char/content');?>">文章月份统计</a></li>
                    <li><a  href="<?php echo U('Admin/Char/category');?>">文章分类统计</a></li>
                </ul>
            </li>
            
            
            
            <li  class="dropdown">
                <a  href="<?php echo U('Admin/Self/updatePwd');?>"  class="dropdown-toggle"  data-toggle="dropdown"><span  id="navbarCurrent">个人设置</span> <b  class="caret"></b></a>
                <ul  class="dropdown-menu"  role="menu">
                    <li><a  href="<?php echo U('Admin/Self/updatePwd');?>">修改密码</a></li>
                </ul>
            </li>
            
            <li  class="dropdown">
                <a  href="<?php echo U('Admin/Admin/adminIndex');?>"  class="dropdown-toggle"  data-toggle="dropdown"><span  id="navbarCurrent">其他</span> <b  class="caret"></b></a>
                <ul  class="dropdown-menu"  role="menu">
                    <li><a  href="<?php echo U('Admin/Freshen/freshen');?>">清除缓存</a></li>
                    <li><a  href="<?php echo U('Admin/Siteoption/index');?>">站点信息</a></li>
                    <li><a  href="<?php echo U('Admin/Admin/help');?>">关于</a></li>
                    <li><a  href="<?php echo U('Admin/Admin/main');?>">系统信息</a></li>
                    <li><a  href="<?php echo U('Admin/Admin/sale');?>">安全退出</a></li>
                    <li><a  href="<?php echo U('/');?>">前台</a></li>
                    <li><a  href="<?php echo U('/Admin/Admin/pc');?>">PC版</a></li>
                    
                </ul>
            </li>
            
            <!--<li  id="navbarNext"><a  href="<?php echo U('/Admin/Admin/pc');?>">PC版 <i  class="icon-angle-right icon-large"></i></a></li>-->
        </ul>
    </div>
</nav>

    <?php else: endif; ?>
   
    <body>
        
        <div  id="main"  style="max-width:700px;margin: 0 auto;">
            <div  class="jumbotron text-center">
                <h1>ClmaoBlog</h1>
                <p  class="text-muted">一个用来记录学习的Blog</p>
                <br>
                <p>
                    <a  target="_blank"  href="http://blog.clmao.com"  class="btn btn-success"><i  class="icon-user"></i> 作者博客</a> &nbsp;
                    <a  target="_blank"  href="http://git.oschina.net/clmao/ClmaoBlog"  class="btn btn-primary"><i  class="icon-download-alt"></i> 源码下载</a> &nbsp;
                    <small  class="text-muted">v1.0 撒哈拉的小猫</small>
                </p>
                <hr>
                <h2  class="text-success">ClmaoBlog构建于众多优秀的开源项目之上</h2>
                <p>非常感谢这些项目！</p>
                <div  class="row">
                    <div  class="col-md-3 col-sm-4"><a  target="_blank"  href="http://www.thinkphp.cn"  class="card"><strong  class="card-heading">ThinkPHP</strong></a></div>
                    <div  class="col-md-3 col-sm-4"><a  target="_blank"  href="https://github.com/easysoft/zui"  class="card"><strong  class="card-heading">ZUI</strong></a></div>
                    <div  class="col-md-3 col-sm-4"><a  target="_blank"  href="https://github.com/ecomfe/echarts"  class="card"><strong  class="card-heading">Echarts</strong></a></div>
                    <div  class="col-md-3 col-sm-4"><a  target="_blank"  href="http://jquery.com/"  class="card"><strong  class="card-heading">jQuery</strong></a></div>
                    <div  class="col-md-3 col-sm-4"><a  target="_blank"  href="http://www.baidu.com"  class="card"><strong  class="card-heading">Uedior</strong></a></div>
                    <div  class="col-md-3 col-sm-4"><a  target="_blank"  href="http://www.ztree.me"  class="card"><strong  class="card-heading">zTree</strong></a></div>
                </div>
            </div>
        </div>

        

    </body>
</html>