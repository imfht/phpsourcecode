<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
session();//权限控制
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $system_sitename;?></title>
  <link rel="stylesheet" href="src/layui/css/layui.css" media="all" />
  <link rel="stylesheet" href="./plugins/font-awesome/css/font-awesome.min.css" media="all" />
  <link rel="stylesheet" href="./src/css/app.css" media="all" />
  <link rel="stylesheet" href="./src/css/themes/red.css" media="all" id="skin" kit-skin />
</head>
<body class="kit-theme">
  <div class="layui-layout layui-layout-admin kit-layout-admin">
    <div class="layui-header">
      <div class="layui-logo"><i class="fa fa-etsy"></i> <i class="fa fa-etsy"> <i class="fa fa-dashcube"></i> <i class="fa fa-opera"></i></div>
      <div class="layui-logo kit-logo-mobile"><i class="fa fa-etsy"></i></div>
      <ul class="layui-nav layui-layout-left kit-nav">
        <li class="layui-nav-item">
            <a href="/" target="_blank">浏览站点</a>
        </li>
        <li class="layui-nav-item">
          <a class="admin-side-full" title="全屏"><i class="fa fa-desktop" aria-hidden="true"></i></a>
        </li>
      </ul>
      <ul class="layui-nav layui-layout-right kit-nav">
        <li class="layui-nav-item">
          <a href="javascript:;">
            <i class="layui-icon">&#xe63f;</i> 皮肤
          </a>
          <dl class="layui-nav-child skin">
            <dd><a href="javascript:;" data-skin="default" style="color:#393D49;"><i class="layui-icon">&#xe658;</i> 默认</a></dd>
            <dd><a href="javascript:;" data-skin="orange" style="color:#ff6700;"><i class="layui-icon">&#xe658;</i> 橘子橙</a></dd>
            <dd><a href="javascript:;" data-skin="green" style="color:#00a65a;"><i class="layui-icon">&#xe658;</i> 原谅绿</a></dd>
            <dd><a href="javascript:;" data-skin="pink" style="color:#FA6086;"><i class="layui-icon">&#xe658;</i> 少女粉</a></dd>
            <dd><a href="javascript:;" data-skin="blue.1" style="color:#00c0ef;"><i class="layui-icon">&#xe658;</i> 天空蓝</a></dd>
            <dd><a href="javascript:;" data-skin="red" style="color:#dd4b39;"><i class="layui-icon">&#xe658;</i> 枫叶红</a></dd>
          </dl>
        </li>
        <li class="layui-nav-item">
          <a href="javascript:;">
            <!--img src="" class="layui-nav-img"--> <?php echo $_COOKIE['u_name']?>
          </a>
          <dl class="layui-nav-child">
            <dd><a href="javascript:;" kit-target data-options="{url:'view/master/page/user/userinfo.php',icon:'&#xe66f;',title:'基本资料',id:'966'}"><span><i class="layui-icon">&#xe66f;</i>基本资料</span></a></dd>
            <dd><a href="javascript:;" kit-target data-options="{url:'view/master/page/user/changepwd.php',icon:'&#xe672;',title:'基本资料',id:'967'}"><span><i class="layui-icon layui-icon-auz"></i>修改密码</span></a></dd>
          </dl>
        </li>
        <li class="layui-nav-item"><a href="javascript:;" class="logout"><i class="fa fa-sign-out" aria-hidden="true"></i> 注销</a></li>
      </ul>
    </div>
    <div class="layui-side layui-bg-black kit-side">
      <div class="layui-side-scroll">
        <div class="kit-side-fold"><i class="fa fa-navicon" aria-hidden="true"></i></div>
        <!-- 左侧导航区域-->
        <ul class="layui-nav layui-nav-tree" lay-filter="kitNavbar" kit-navbar>
          <li class="layui-nav-item layui-nav-itemed">
            <a class="" href="javascript:;"><i class="fa fa-vimeo-square" aria-hidden="true"></i><span> 留言管理</span></a>
            <dl class="layui-nav-child">
              <dd>
                <a href="javascript:;" kit-target data-options="{url:'view/master/page/book/',icon:'&#xe62a;',title:'留言管理',id:'1'}">
                  <i class="layui-icon">&#xe62a;</i><span> 留言管理</span></a>
              </dd>
              <dd>
                <a href="javascript:;" kit-target data-options="{url:'view/master/page/type/',icon:'&#xe60a;',title:'分类管理',id:'2'}">
                  <i class="fa fa-th-list" aria-hidden="true"></i><span> 分类管理</span></a>
              </dd>
              
            </dl>
          </li>
          <li class="layui-nav-item">
            <a href="javascript:;"><i class="fa fa-cog" aria-hidden="true"></i><span> 系统设置</span></a>
            <dl class="layui-nav-child">
              <dd>
              	<a href="javascript:;" kit-target data-options="{url:'view/master/page/system/',icon:'&#xe631;',title:'系统设置',id:'11'}"><i class="layui-icon">&#xe631;</i><span>参数设置</span></a>
              </dd>
              <dd>
              	<a href="javascript:;" kit-target data-options="{url:'view/master/page/log',icon:'&#xe658;',title:'日志记录',id:'12'}"><i class="fa fa-book" aria-hidden="true"></i><span>日志记录</span></a>
              </dd>
              <dd>
                <a href="javascript:;" kit-target data-options="{url:'http://guestbook.eedo.net/notice.html',icon:'&#xe607;',title:'官方公告',id:'99'}"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><span>官方公告</span></a>
              </dd>
            </dl>
          </li>
          
        </ul>
      </div>
    </div>
    <div class="layui-body" id="container">
      <!-- 内容主体区域 -->
      <div style="padding: 15px;"><i class="layui-icon layui-anim layui-anim-rotate layui-anim-loop">&#xe63e;</i> 加载中，请稍后...</div>
    </div>
    <div class="layui-footer">
      <!-- 底部固定区域 -->
      <?php echo $system_sitename." - ".$system_version ."&nbsp;&nbsp;".$system_copyright;?>
    </div>
  </div>
  <script src="/src/layui/layui.js"></script>
  <script>
    var message;
    layui.config({
      base: 'src/js/',
      version: '1.0.1',
    }).use(['app', 'message'], function() {
      var app = layui.app,
        $ = layui.jquery,
        layer = layui.layer;
      //将message设置为全局以便子页面调用
      message = layui.message;
      //主入口
      app.set({
        minurl:'/view/master/main.php',//主页面
        type: 'iframe'//页面加载方式
      }).init();
      //注销事件
      $('.logout').on('click', function() {
      layer.open({
			title: '注销？',
			closeBtn : false,
			content: '确定注销当前账号？'
			,btn: ['注销','取消']
			,btn1: function(){
        $.ajax({            
          url:"/view/master/logout.php",
          success: function(data){
            if(data.trim()=="OK")//要加上去空格，防止内容里面有空格引起错误。
            {
              layui.use('layer', function(){
    					layer.msg('已注销，请重新登陆',{shade:0.8});
    					});
    					//刷新当前页
    					setTimeout(function(){  //使用  setTimeout（）方法设定定时2000毫秒
    					window.location.href="/";//页面刷新
    					},2000);
            }else{
              alert(data.trim());
            }
          }
        });
			},btn2: function(index, layero){  
			    layer.close(index)
			  return false; 
			}    
			});
      });
      //END注销
      $('dl.skin > dd').on('click', function() {
        var $that = $(this);
        var skin = $that.children('a').data('skin');
        switchSkin(skin);
      });
      var setSkin = function(value) {
          layui.data('kit_skin', {
            key: 'skin',
            value: value
          });
        },
        getSkinName = function() {
          return layui.data('kit_skin').skin;
        },
        switchSkin = function(value) {
          var _target = $('link[kit-skin]')[0];
          _target.href = _target.href.substring(0, _target.href.lastIndexOf('/') + 1) + value + _target.href.substring(_target.href.lastIndexOf('.'));
          setSkin(value);
        },
        initSkin = function() {
          var skin = getSkinName();
          switchSkin(skin === undefined ? 'red' : skin);
        }();
        //全屏
         $('.admin-side-full').on('click', function () {
	        var docElm = document.documentElement;
	        //W3C  
	        if (docElm.requestFullscreen) {
	            docElm.requestFullscreen();
	        }
	        //FireFox  
	        else if (docElm.mozRequestFullScreen) {
	            docElm.mozRequestFullScreen();
	        }
	        //Chrome  
	        else if (docElm.webkitRequestFullScreen) {
	            docElm.webkitRequestFullScreen();
	        }
	        //IE11
	        else if (elem.msRequestFullscreen) {
	            elem.msRequestFullscreen();
	        }
	        layer.msg('按Esc即可退出全屏');
	    });
    });
  </script>
  <script>
    var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?448f02fca78a892e6d9c5f1c599ff906";
      var s = document.getElementsByTagName("script")[0]; 
      s.parentNode.insertBefore(hm, s);
    })();
  </script>
</body>
</html>