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
   
  <script type="text/javascript" src="/Public/ueditor/ueditor.config.js"></script>    
    <script type="text/javascript" src="/Public/ueditor/ueditor.all.min.js"></script>
    <script>
    $(function(){
        var url='<?php echo U('Admin/Ueditor/Index');?>';
        var ue = UE.getEditor('container',{
            serverUrl :url,
            UEDITOR_HOME_URL:'/Public/ueditor/',
        });

        ue.ready(function(){
            ue.execCommand('serverparam', {
                'userid': '1',
               'username': 'admin',
            });
        });
      

    })
     
    </script>
     <script type="text/javascript">
       $(document).ready(function() {
           
             $(".saveContent").click(function() {
              var action = '<?php echo U('Admin/Admin/addDraftProcess');?>';
              $("form:first").attr("action",action);
              $("form:first").submit();
             });
        });
       
     </script>

 <body>
     <div class='panel'>
        <div class='panel-heading'>撰写文章</div>
        <div class='panel-body'>
        <form class="form-horizontal" role="form" method='post'  action="<?php echo U('Admin/Admin/editContentProcess');?>">
           <div class="form-group">
            <label class="col-md-2 control-label required">标题</label>
            <div class="col-md-10">
               <input type="hidden" name="id" value="<?php echo ($content["id"]); ?>">
              <input type="hidden" name="status" value="1">
               <input type='text' name='title' id='title' value='<?php echo ($content["title"]); ?>' class='form-control' placeholder=''/>
            </div>
          </div>
            
           <div class="form-group">
            <label class="col-md-2  control-label required">来源</label>
            <div class="col-md-4">
                <input type='text' name='author' id='author' value='<?php echo ($content["author"]); ?>' class='form-control' />
            </div>
          </div>  
            
          <div class="form-group">
            <label class="col-md-2 control-label required">分类</label>
            <div class="col-md-4">
                <select name='c_id'>
                    <?php if(is_array($category)): foreach($category as $key=>$category): ?><option value ="<?php echo ($category["id"]); ?>"<?php if(($category["id"] == $content['c_id'])): ?>selected='selected'<?php else: endif; ?>><?php echo ($category["title"]); ?></option><?php endforeach; endif; ?>
                </select>
          
            </div>
          </div>
             
         

          <div class="form-group">
            <label class="col-md-2 control-label">定时发布</label>
            <div class="col-md-4">
              <div class="input-group">
                <input type='datetime-local' name='time' id='date' class='form-control' />
                <span class="input-group-btn"> 留空为即时发布 </span>
              </div>
            </div>
          </div>


          <div class="form-group">
            <label class="col-md-2 control-label">内容</label>
            <div class="col-md-10">
              <script id="container" name="content" type="text/plain"><?php echo ($content["content"]); ?></script>
            </div>
          </div>
        

          <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <input type="submit" value='更新' class="btn btn-primary" />
                <input type="button" value='保存草稿'class="saveContent btn btn-primary" />
            </div>
          </div>
            
        </form>
        </div>
      </div>
     
  
 </body>
</html>